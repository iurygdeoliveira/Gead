<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Discipline;
use App\Models\Evaluation;
use App\Models\Student;
use App\Models\Teacher;

class EvaluationAnalyticsService
{
    /**
     * Retorna a query base de avaliações, ignorando alunos dispensados
     *
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Evaluation>
     */
    private function getBaseEvaluationQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Evaluation::query()
            ->whereHas('classEnrollment.enrollment.student', function ($q) {
                $q->where('is_dispensed_from_evaluations', false);
            });
    }

    /**
     * KPI 1: Dimensões mais fracas (médias das 6 dimensões no campus)
     *
     * @return array<string, float>
     */
    public function getWeakestDimensions(?int $teamId = null): array
    {
        $query = $this->getBaseEvaluationQuery();

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        $avg = $query->selectRaw('
            ROUND(AVG(planning_score), 2) as planning,
            ROUND(AVG(posture_score), 2) as posture,
            ROUND(AVG(attendance_score), 2) as attendance,
            ROUND(AVG(punctuality_score), 2) as punctuality,
            ROUND(AVG(execution_score), 2) as execution,
            ROUND(AVG(assessment_score), 2) as assessment
        ')->first();

        return [
            'Planejamento' => (float) ($avg->planning ?? 0),
            'Postura' => (float) ($avg->posture ?? 0),
            'Assiduidade' => (float) ($avg->attendance ?? 0),
            'Pontualidade' => (float) ($avg->punctuality ?? 0),
            'Execução' => (float) ($avg->execution ?? 0),
            'Avaliação' => (float) ($avg->assessment ?? 0),
        ];
    }

    /**
     * KPI 2: Taxa de adesão (porcentagem global + por curso)
     *
     * @return array{global_rate: float, courses: array<int, array{course_name: string, completed: int, total: int, rate: float}>}
     */
    public function getParticipationRates(?int $teamId = null): array
    {
        $coursesQuery = Course::query();
        if ($teamId) {
            $coursesQuery->where('team_id', $teamId);
        }
        $courses = $coursesQuery->get();

        $courseStats = [];
        $totalCompletedGlobal = 0;
        $totalExpectedGlobal = 0;

        foreach ($courses as $course) {
            $status = $course->getEvaluationsCompletionStatus();
            $completed = $status['completed'];
            $expected = $status['expected'];
            $rate = $expected > 0 ? round(($completed / $expected) * 100, 1) : 0.0;

            $totalCompletedGlobal += $completed;
            $totalExpectedGlobal += $expected;

            $courseStats[] = [
                'course_name' => $course->short_name,
                'completed' => $completed,
                'total' => $expected,
                'rate' => $rate,
            ];
        }

        $globalRate = $totalExpectedGlobal > 0 ? round(($totalCompletedGlobal / $totalExpectedGlobal) * 100, 1) : 0.0;

        return [
            'global_rate' => $globalRate,
            'courses' => $courseStats,
        ];
    }

    /**
     * KPI 3: Evolução temporal das 6 dimensões por semestre letivo
     *
     * @return array{terms: array<string>, series: array<string, array<float>>}
     */
    public function getTemporalEvolution(?int $teamId = null, ?int $courseId = null): array
    {
        $terms = AcademicTerm::orderBy('start_date')->get();

        $dimensions = [
            'planning_score' => 'Planejamento',
            'posture_score' => 'Postura',
            'attendance_score' => 'Assiduidade',
            'punctuality_score' => 'Pontualidade',
            'execution_score' => 'Execução',
            'assessment_score' => 'Avaliação',
        ];

        $resultSeries = [];
        foreach ($dimensions as $name) {
            $resultSeries[$name] = [];
        }

        $termNames = [];

        foreach ($terms as $term) {
            $termNames[] = $term->name;

            $query = $this->getBaseEvaluationQuery()
                ->whereHas('courseClassDiscipline.courseClass', function ($q) use ($term, $courseId) {
                    $q->where('academic_term_id', $term->id);
                    if ($courseId) {
                        $q->where('course_id', $courseId);
                    }
                });

            if ($teamId) {
                $query->where('team_id', $teamId);
            }

            $avgs = $query->selectRaw('
                ROUND(AVG(planning_score), 2) as planning,
                ROUND(AVG(posture_score), 2) as posture,
                ROUND(AVG(attendance_score), 2) as attendance,
                ROUND(AVG(punctuality_score), 2) as punctuality,
                ROUND(AVG(execution_score), 2) as execution,
                ROUND(AVG(assessment_score), 2) as assessment
            ')->first();

            $resultSeries['Planejamento'][] = (float) ($avgs->planning ?? 0);
            $resultSeries['Postura'][] = (float) ($avgs->posture ?? 0);
            $resultSeries['Assiduidade'][] = (float) ($avgs->attendance ?? 0);
            $resultSeries['Pontualidade'][] = (float) ($avgs->punctuality ?? 0);
            $resultSeries['Execução'][] = (float) ($avgs->execution ?? 0);
            $resultSeries['Avaliação'][] = (float) ($avgs->assessment ?? 0);
        }

        return [
            'terms' => $termNames,
            'series' => $resultSeries,
        ];
    }

    /**
     * KPI 4: Distribuição das notas (0 a 10)
     *
     * @return array<int, int>
     */
    public function getGradeDistribution(?int $teamId = null, ?int $courseId = null): array
    {
        $distribution = array_fill(0, 11, 0); // 0 a 10

        $query = $this->getBaseEvaluationQuery();

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        if ($courseId) {
            $query->whereHas('courseClassDiscipline.courseClass', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $scores = $query->get([
            'planning_score', 'posture_score', 'attendance_score',
            'punctuality_score', 'execution_score', 'assessment_score',
        ]);

        foreach ($scores as $eval) {
            foreach (['planning_score', 'posture_score', 'attendance_score', 'punctuality_score', 'execution_score', 'assessment_score'] as $field) {
                $val = $eval->{$field};
                if ($val !== null) {
                    $intVal = (int) round((float) $val);
                    $intVal = max(0, min(10, $intVal));
                    $distribution[$intVal]++;
                }
            }
        }

        return $distribution;
    }

    /**
     * KPI 5A: Perfil de desempenho individual de um professor
     *
     * @return array{teacher_scores: array<string, float>, course_avg_scores: array<string, float>}
     */
    public function getTeacherProfile(int $teacherId, ?int $teamId = null): array
    {
        $teacher = Teacher::find($teacherId);
        if (! $teacher) {
            return [
                'teacher_scores' => array_fill_keys(['Planejamento', 'Postura', 'Assiduidade', 'Pontualidade', 'Execução', 'Avaliação'], 0.0),
                'course_avg_scores' => array_fill_keys(['Planejamento', 'Postura', 'Assiduidade', 'Pontualidade', 'Execução', 'Avaliação'], 0.0),
            ];
        }

        $teacherEvalQuery = $this->getBaseEvaluationQuery()
            ->whereHas('courseClassDiscipline', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });

        if ($teamId) {
            $teacherEvalQuery->where('team_id', $teamId);
        }

        $teacherAvg = $teacherEvalQuery->selectRaw('
            ROUND(AVG(planning_score), 2) as planning,
            ROUND(AVG(posture_score), 2) as posture,
            ROUND(AVG(attendance_score), 2) as attendance,
            ROUND(AVG(punctuality_score), 2) as punctuality,
            ROUND(AVG(execution_score), 2) as execution,
            ROUND(AVG(assessment_score), 2) as assessment
        ')->first();

        // Média do campus/geral para comparação
        $campusAvg = $this->getWeakestDimensions($teamId);

        return [
            'teacher_scores' => [
                'Planejamento' => (float) ($teacherAvg->planning ?? 0),
                'Postura' => (float) ($teacherAvg->posture ?? 0),
                'Assiduidade' => (float) ($teacherAvg->attendance ?? 0),
                'Pontualidade' => (float) ($teacherAvg->punctuality ?? 0),
                'Execução' => (float) ($teacherAvg->execution ?? 0),
                'Avaliação' => (float) ($teacherAvg->assessment ?? 0),
            ],
            'course_avg_scores' => $campusAvg,
        ];
    }

    /**
     * KPI 5B: Perfil Consolidado por Turma do Curso (cada série é a média/somatória dos professores de uma turma)
     *
     * @return array{classes: array<string, array<string, float>>, dimensions: array<string>}
     */
    public function getCourseClassTeachersRadar(int $courseId, ?int $teamId = null): array
    {
        $courseClasses = CourseClass::where('course_id', $courseId);
        if ($teamId) {
            $courseClasses->where('team_id', $teamId);
        }
        $classes = $courseClasses->get();

        $dimensions = ['Planejamento', 'Postura', 'Assiduidade', 'Pontualidade', 'Execução', 'Avaliação'];
        $classData = [];

        foreach ($classes as $class) {
            $avg = $this->getBaseEvaluationQuery()
                ->whereHas('courseClassDiscipline', function ($q) use ($class) {
                    $q->where('course_class_id', $class->id);
                })
                ->selectRaw('
                    ROUND(AVG(planning_score), 2) as planning,
                    ROUND(AVG(posture_score), 2) as posture,
                    ROUND(AVG(attendance_score), 2) as attendance,
                    ROUND(AVG(punctuality_score), 2) as punctuality,
                    ROUND(AVG(execution_score), 2) as execution,
                    ROUND(AVG(assessment_score), 2) as assessment
                ')->first();

            $classData[$class->name ?? "Turma {$class->code}"] = [
                'Planejamento' => (float) ($avg->planning ?? 0),
                'Postura' => (float) ($avg->posture ?? 0),
                'Assiduidade' => (float) ($avg->attendance ?? 0),
                'Pontualidade' => (float) ($avg->punctuality ?? 0),
                'Execução' => (float) ($avg->execution ?? 0),
                'Avaliação' => (float) ($avg->assessment ?? 0),
            ];
        }

        return [
            'classes' => $classData,
            'dimensions' => $dimensions,
        ];
    }

    /**
     * KPI 6: Disciplinas com piores índices
     *
     * @return array<int, array{discipline_name: string, avg_score: float}>
     */
    public function getProblematicDisciplines(?int $teamId = null, ?int $courseId = null, ?int $courseClassId = null, int $limit = 10): array
    {
        $query = Discipline::query()
            ->join('course_class_disciplines', 'disciplines.id', '=', 'course_class_disciplines.discipline_id')
            ->join('course_classes', 'course_class_disciplines.course_class_id', '=', 'course_classes.id')
            ->join('evaluations', 'course_class_disciplines.id', '=', 'evaluations.course_class_discipline_id')
            ->join('class_enrollments', 'evaluations.class_enrollment_id', '=', 'class_enrollments.id')
            ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->where('students.is_dispensed_from_evaluations', false);

        if ($teamId) {
            $query->where('evaluations.team_id', $teamId);
        }

        if ($courseClassId) {
            $query->where('course_classes.id', $courseClassId);
        } elseif ($courseId) {
            $query->where('course_classes.course_id', $courseId);
        }

        $results = $query->selectRaw('
            disciplines.name as discipline_name,
            ROUND(AVG((
                COALESCE(evaluations.planning_score, 0) +
                COALESCE(evaluations.posture_score, 0) +
                COALESCE(evaluations.attendance_score, 0) +
                COALESCE(evaluations.punctuality_score, 0) +
                COALESCE(evaluations.execution_score, 0) +
                COALESCE(evaluations.assessment_score, 0)
            ) / 6.0), 2) as avg_score
        ')
            ->groupBy('disciplines.id', 'disciplines.name')
            ->orderBy('avg_score', 'asc')
            ->limit($limit)
            ->get();

        return $results->map(function ($row) {
            /** @var object{discipline_name: string, avg_score: float|string} $row */
            return [
                'discipline_name' => (string) $row->discipline_name,
                'avg_score' => (float) $row->avg_score,
            ];
        })->toArray();
    }

    /**
     * KPI 7: Comparativo entre Turmas de um mesmo curso
     */
    public function getClassComparisonRadar(int $courseId, ?int $teamId = null): array
    {
        return $this->getCourseClassTeachersRadar($courseId, $teamId);
    }

    /**
     * KPI 8: Alunos dispensados da avaliação
     *
     * @return array{active: int, dispensed: int, percentage: float, courses: array<int, array{course_name: string, total: int, dispensed: int, percent: float}>}
     */
    public function getDispensedStudentsStats(?int $teamId = null): array
    {
        $studentsQuery = Student::query();
        if ($teamId) {
            $studentsQuery->where('team_id', $teamId);
        }

        $totalStudents = (clone $studentsQuery)->count();
        $dispensedStudents = (clone $studentsQuery)->where('is_dispensed_from_evaluations', true)->count();
        $activeStudents = $totalStudents - $dispensedStudents;
        $globalPercentage = $totalStudents > 0 ? round(($dispensedStudents / $totalStudents) * 100, 1) : 0.0;

        $coursesQuery = Course::query();
        if ($teamId) {
            $coursesQuery->where('team_id', $teamId);
        }
        $courses = $coursesQuery->get();

        $courseStats = [];
        foreach ($courses as $course) {
            $cTotal = Student::whereHas('enrollments', fn ($q) => $q->where('course_id', $course->id))->count();
            $cDispensed = Student::whereHas('enrollments', fn ($q) => $q->where('course_id', $course->id))
                ->where('is_dispensed_from_evaluations', true)
                ->count();
            $cPercent = $cTotal > 0 ? round(($cDispensed / $cTotal) * 100, 1) : 0.0;

            $courseStats[] = [
                'course_name' => $course->name,
                'total' => $cTotal,
                'dispensed' => $cDispensed,
                'percent' => $cPercent,
            ];
        }

        return [
            'active' => $activeStudents,
            'dispensed' => $dispensedStudents,
            'percentage' => $globalPercentage,
            'courses' => $courseStats,
        ];
    }

    /**
     * KPI 9: Matriz de Correlação entre as 6 dimensões (Pearson Correlation Matrix)
     *
     * @return array{matrix: array<string, array<string, float>>, dimensions: array<string>}
     */
    public function getDimensionCorrelationMatrix(?int $teamId = null, ?int $courseId = null): array
    {
        $dimensions = [
            'planning_score' => 'Planejamento',
            'posture_score' => 'Postura',
            'attendance_score' => 'Assiduidade',
            'punctuality_score' => 'Pontualidade',
            'execution_score' => 'Execução',
            'assessment_score' => 'Avaliação',
        ];

        $query = $this->getBaseEvaluationQuery()
            ->whereNotNull('planning_score')
            ->whereNotNull('posture_score')
            ->whereNotNull('attendance_score')
            ->whereNotNull('punctuality_score')
            ->whereNotNull('execution_score')
            ->whereNotNull('assessment_score');

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        if ($courseId) {
            $query->whereHas('courseClassDiscipline', function ($q) use ($courseId) {
                $q->whereHas('courseClass', function ($q2) use ($courseId) {
                    $q2->where('course_id', $courseId);
                });
            });
        }

        $data = $query->get(array_keys($dimensions));

        $dimKeys = array_keys($dimensions);
        $dimNames = array_values($dimensions);
        $matrix = [];

        // Calcular médias e desvios-padrão
        $means = [];
        $stdevs = [];
        $n = count($data);

        if ($n < 2) {
            // Retorna matriz 1.00 genérica se não houver dados suficientes
            foreach ($dimNames as $name1) {
                foreach ($dimNames as $name2) {
                    $matrix[$name1][$name2] = $name1 === $name2 ? 1.0 : 0.0;
                }
            }

            return ['matrix' => $matrix, 'dimensions' => $dimNames];
        }

        foreach ($dimKeys as $key) {
            $vals = $data->pluck($key)->map(fn ($v) => (float) $v)->toArray();
            $mean = array_sum($vals) / $n;
            $means[$key] = $mean;

            $variance = array_reduce($vals, fn ($carry, $v) => $carry + pow($v - $mean, 2), 0.0) / ($n - 1);
            $stdevs[$key] = sqrt($variance);
        }

        foreach ($dimKeys as $i => $key1) {
            $name1 = $dimensions[$key1];
            foreach ($dimKeys as $j => $key2) {
                $name2 = $dimensions[$key2];

                if ($i === $j) {
                    $matrix[$name1][$name2] = 1.0;

                    continue;
                }

                if ($stdevs[$key1] == 0 || $stdevs[$key2] == 0) {
                    $matrix[$name1][$name2] = 0.0;

                    continue;
                }

                $covariance = 0.0;
                foreach ($data as $row) {
                    $covariance += ((float) $row->{$key1} - $means[$key1]) * ((float) $row->{$key2} - $means[$key2]);
                }
                $covariance /= ($n - 1);

                $corr = $covariance / ($stdevs[$key1] * $stdevs[$key2]);
                $matrix[$name1][$name2] = round(max(-1.0, min(1.0, $corr)), 2);
            }
        }

        return [
            'matrix' => $matrix,
            'dimensions' => $dimNames,
        ];
    }
}
