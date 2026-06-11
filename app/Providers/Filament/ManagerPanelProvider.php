<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Team;
use Filament\Pages\Dashboard;
use Filament\Panel;

class ManagerPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        $panel = parent::panel($panel);

        $panel = $panel
            ->tenant(Team::class, slugAttribute: 'slug', ownershipRelationship: 'teams')
            ->tenantMenu(false)
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->resources([\App\Filament\Resources\Teachers\TeacherResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->tenantMiddleware([
                TeamSyncMiddleware::class,
            ], isPersistent: true);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'manager';
    }

    protected function getPanelPath(): string
    {
        return 'manager';
    }
}
