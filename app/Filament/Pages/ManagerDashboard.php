<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\CourseEvaluationsWidget;
use App\Filament\Widgets\DispensedStudentsWidget;
use App\Filament\Widgets\EvaluationsOverviewWidget;
use App\Filament\Widgets\GenerateEvaluationsWidget;
use App\Filament\Widgets\StudentsWithoutClassWidget;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Team;
use Filament\Pages\Dashboard as BaseDashboard;

class ManagerDashboard extends BaseDashboard
{
    #[\Override]
    public function getWidgets(): array
    {
        $widgets = [
            GenerateEvaluationsWidget::class,
            EvaluationsOverviewWidget::class,
        ];

        /** @var Team|null $tenant */
        $tenant = filament()->getTenant();
        $teamId = $tenant?->id;

        if ($teamId) {
            $activeCourseIds = CourseClass::whereHas('classEnrollments')
                ->pluck('course_id')
                ->unique();

            $courses = Course::whereIn('id', $activeCourseIds)->where('team_id', $teamId)->get();

            foreach ($courses as $course) {
                $widgets[] = CourseEvaluationsWidget::make([
                    'courseId' => $course->id,
                    'courseName' => $course->name,
                ]);
            }
        }

        return $widgets;
    }
}
