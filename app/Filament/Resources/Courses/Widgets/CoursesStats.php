<?php

namespace App\Filament\Resources\Courses\Widgets;

use App\Models\Course;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;

class CoursesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Course::query();
        
        $currentTeam = Filament::getTenant();
        if ($currentTeam) {
            $query->where('team_id', $currentTeam->getKey());
        }

        return [
            Stat::make('Total de Cursos', $query->count())
                ->description('Cursos cadastrados neste campus')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
