<?php

namespace App\Filament\Resources\Students\Widgets;

use App\Models\Student;
use App\Models\Evaluation;
use App\Models\CourseClassDiscipline;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $currentTeam = Filament::getTenant();

        $query = Student::query()
            ->select('students.*')
            ->addSelect([
                'evaluations_done' => Evaluation::selectRaw('count(*)')
                    ->join('class_enrollments', 'evaluations.class_enrollment_id', '=', 'class_enrollments.id')
                    ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
                    ->whereColumn('enrollments.student_id', 'students.id')
                    ->whereNotNull('evaluations.planning_score'),
                
                'evaluations_total' => CourseClassDiscipline::selectRaw('count(*)')
                    ->join('class_enrollments', 'course_class_disciplines.course_class_id', '=', 'class_enrollments.course_class_id')
                    ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
                    ->whereColumn('enrollments.student_id', 'students.id'),
            ]);

        if ($currentTeam) {
            $query->where('team_id', $currentTeam->getKey());
        }

        // Only active/released students
        $query->where(function ($sub) {
            $sub->whereNull('user_id')
                ->orWhereHas('user', function ($uq) {
                    $uq->where('is_suspended', false);
                });
        });

        $students = $query->get();
        $totalStudents = $students->count();

        $completas = 0;
        $incompletas = 0;

        foreach ($students as $student) {
            $done = (int) $student->evaluations_done;
            $total = (int) $student->evaluations_total;

            if ($total > 0) {
                if ($done === $total) {
                    $completas++;
                } else {
                    $incompletas++;
                }
            }
        }

        return [
            Stat::make('Total de Alunos', $totalStudents)
                ->description('Alunos ativos neste campus')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Alunos com Avaliações Completas', $completas)
                ->description('Concluíram todas as avaliações')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Alunos com Avaliações Incompletas', $incompletas)
                ->description('Possuem avaliações pendentes')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
