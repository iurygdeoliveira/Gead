<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\StudentDashboard;
use App\Filament\Widgets\StudentDisciplinesWidget;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Team;
use Filament\Panel;

class StudentPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        // Particularidades do painel discente (student)
        $panel = $panel
            ->tenant(Team::class, slugAttribute: 'slug', ownershipRelationship: 'teams')
            ->tenantMenu(false)
            ->resources([
            ])
            ->pages([
                StudentDashboard::class,
            ])
            ->widgets([
                StudentDisciplinesWidget::class,
            ])
            ->tenantMiddleware([
                TeamSyncMiddleware::class,
                \App\Http\Middleware\CheckStudentEvaluationLimit::class,
            ], isPersistent: true);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'student';
    }

    protected function getPanelPath(): string
    {
        return 'student';
    }
}
