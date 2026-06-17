<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\ClassEnrollment;
use App\Models\Evaluation;
use Filament\Widgets\Widget;

class CourseEvaluationsWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.course-evaluations-widget';

    public ?int $courseId = null;

    public static function canView(): bool
    {
        return filament()->getCurrentPanel()?->getId() === 'manager';
    }

    public function getData(): array
    {
        $teamId = filament()->getTenant()?->id;

        if (! $teamId || ! $this->courseId) {
            return [];
        }

        $course = Course::where('id', $this->courseId)->where('team_id', $teamId)->first();
        if (! $course) {
            return [];
        }

        $courseClasses = CourseClass::where('course_id', $this->courseId)->where('team_id', $teamId)->get();
        $classIds = $courseClasses->pluck('id')->toArray();

        // Potential calculation based on class enrollments and course class disciplines
        $enrollmentCountsByClass = ClassEnrollment::whereIn('course_class_id', $classIds)
            ->whereHas('enrollment.student', function ($query) {
                $query->whereDoesntHave('enrollments', function ($q) {
                    $q->whereDoesntHave('classEnrollments');
                });
            })
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        $disciplineCountsByClass = CourseClassDiscipline::whereIn('course_class_id', $classIds)
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        $totalPotential = 0;
        foreach ($classIds as $classId) {
            $totalPotential += ($enrollmentCountsByClass[$classId] ?? 0) * ($disciplineCountsByClass[$classId] ?? 0);
        }

        $evaluations = Evaluation::where('team_id', $teamId)
            ->whereHas('courseClassDiscipline.courseClass', function ($query) {
                $query->where('course_id', $this->courseId);
            })
            ->whereHas('classEnrollment.enrollment.student', function ($query) {
                $query->whereDoesntHave('enrollments', function ($q) {
                    $q->whereDoesntHave('classEnrollments');
                });
            })
            ->get();

        $realizadas = $evaluations->whereNotNull('planning_score')->count();

        // Se totalPotential for menor que realizadas (caso de dados sujos), fixamos no max()
        $total = max($totalPotential, $realizadas, $evaluations->count());
        $naoRealizadas = max(0, $total - $realizadas);

        $divider = $total > 0 ? $total : 1;

        return [
            'name' => $course->name,
            'realizadas' => $realizadas,
            'realizadas_pct' => ($realizadas / $divider) * 100,
            'nao_realizadas' => $naoRealizadas,
            'nao_realizadas_pct' => ($naoRealizadas / $divider) * 100,
            'total' => $total,
        ];
    }
}
