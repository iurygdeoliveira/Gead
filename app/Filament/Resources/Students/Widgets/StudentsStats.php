<?php

namespace App\Filament\Resources\Students\Widgets;

use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Student::query();

        $currentTeam = Filament::getTenant();
        if ($currentTeam) {
            $query->where('team_id', $currentTeam->getKey());
        }

        return [
            Stat::make('Total de Alunos', $query->count())
                ->description('Alunos cadastrados neste campus')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
