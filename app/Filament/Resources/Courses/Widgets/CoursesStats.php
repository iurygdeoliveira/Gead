<?php

namespace App\Filament\Resources\Courses\Widgets;

use App\Models\Course;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CoursesStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    #[\Override]
    protected function getStats(): array
    {
        $query = Course::query();

        $currentTeam = Filament::getTenant();
        if ($currentTeam) {
            $query->where('team_id', $currentTeam->getKey());
        }

        $courses = $query->get();
        $totalCourses = $courses->count();

        $completas = 0;
        $incompletas = 0;

        foreach ($courses as $course) {
            $status = $course->getEvaluationsCompletionStatus();
            if ($status['expected'] > 0) {
                if ($status['completed'] === $status['expected']) {
                    $completas++;
                } else {
                    $incompletas++;
                }
            }
        }

        return [
            Stat::make('Total de Cursos', $totalCourses)
                ->description('Cursos cadastrados neste campus')
                ->descriptionIcon(Heroicon::AcademicCap)
                ->color('primary'),

            Stat::make('Cursos com Avaliações Completas', $completas)
                ->description('Avaliações Completas')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('success'),

            Stat::make('Cursos com Avaliações Incompletas', $incompletas)
                ->description('Avaliações Pendentes')
                ->descriptionIcon(Heroicon::XCircle)
                ->color('danger'),
        ];
    }
}
