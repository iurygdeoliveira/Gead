<?php

namespace App\Filament\Resources\Teachers\Widgets;

use App\Models\Teacher;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeachersStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    #[\Override]
    protected function getStats(): array
    {
        $query = Teacher::query();

        $currentTeam = Filament::getTenant();
        if ($currentTeam) {
            $query->where('team_id', $currentTeam->getKey());
        }

        $teachers = $query->with('taughtDisciplines')->get();
        $totalTeachers = $teachers->count();

        $completas = 0;
        $incompletas = 0;

        foreach ($teachers as $teacher) {
            $status = $teacher->getEvaluationsCompletionStatus();
            if ($status['expected'] > 0) {
                if ($status['completed'] === $status['expected']) {
                    $completas++;
                } else {
                    $incompletas++;
                }
            }
        }

        return [
            Stat::make('Total de Professores', $totalTeachers)
                ->description('Professores cadastrados neste campus')
                ->descriptionIcon(Heroicon::Users)
                ->color('primary'),

            Stat::make('Professores com Avaliações Completas', $completas)
                ->description('Todas as turmas responderam')
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('success'),

            Stat::make('Professores com Avaliações Incompletas', $incompletas)
                ->description('Possuem turmas com avaliações pendentes')
                ->descriptionIcon(Heroicon::XCircle)
                ->color('danger'),
        ];
    }
}
