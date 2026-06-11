<?php

namespace App\Filament\Resources\Teachers\Widgets;

use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;

class TeachersStats extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Teacher::query();
        
        $currentTeam = Filament::getTenant();
        if ($currentTeam) {
            $query->where('team_id', $currentTeam->getKey());
        }

        return [
            Stat::make('Total de Professores', $query->count())
                ->description('Professores cadastrados neste campus')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
