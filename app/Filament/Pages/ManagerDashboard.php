<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\CourseEvaluationsWidget;
use App\Filament\Widgets\EvaluationsOverviewWidget;
use App\Filament\Widgets\GenerateEvaluationsWidget;
use App\Filament\Widgets\SelectPeriodWidget;
use App\Models\Course;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class ManagerDashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $widgets = [
            SelectPeriodWidget::class,
            GenerateEvaluationsWidget::class,
            EvaluationsOverviewWidget::class,
        ];

        $teamId = filament()->getTenant()?->id;

        if ($teamId) {
            $courses = Course::where('team_id', $teamId)->get();

            foreach ($courses as $course) {
                $widgets[] = CourseEvaluationsWidget::make([
                    'courseId' => $course->id,
                ]);
            }
        }

        return $widgets;
    }
}
