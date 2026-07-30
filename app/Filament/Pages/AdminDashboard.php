<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\CourseEvaluationsWidget;
use App\Filament\Widgets\CustomStats;
use App\Filament\Widgets\EvaluationsOverviewWidget;
use App\Filament\Widgets\GenerateEvaluationsWidget;
use App\Filament\Widgets\StudentDisciplinesWidget;
use App\Filament\Widgets\SystemStats;
use App\Models\Course;
use App\Models\CourseClass;

class AdminDashboard extends ManagerDashboard
{
    protected static ?string $slug = 'dashboard';

    protected static string $routePath = '/';

    #[\Override]
    public function getWidgets(): array
    {
        $widgets = [
            CustomStats::class,
            SystemStats::class,
            GenerateEvaluationsWidget::class,
            EvaluationsOverviewWidget::class,
            StudentDisciplinesWidget::class,
        ];

        $activeCourseIds = CourseClass::whereHas('classEnrollments')
            ->pluck('course_id')
            ->unique();

        $courses = Course::whereIn('id', $activeCourseIds)->get();

        foreach ($courses as $course) {
            $widgets[] = CourseEvaluationsWidget::make([
                'courseId' => $course->id,
                'courseName' => $course->name,
            ]);
        }

        return $widgets;
    }
}
