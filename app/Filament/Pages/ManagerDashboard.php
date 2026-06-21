<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\CourseEvaluationsWidget;
use App\Filament\Widgets\EvaluationsOverviewWidget;
use App\Filament\Widgets\GenerateEvaluationsWidget;
use App\Filament\Widgets\StudentsWithoutClassWidget;
use App\Models\Course;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class ManagerDashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $widgets = [
            GenerateEvaluationsWidget::class,
            StudentsWithoutClassWidget::class,
            EvaluationsOverviewWidget::class,
        ];

        $teamId = filament()->getTenant()?->id;

        if ($teamId) {
            $activeCourseIds = \App\Models\CourseClass::whereHas('classEnrollments')
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
