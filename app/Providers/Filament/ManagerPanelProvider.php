<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Teachers\TeacherResource;
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
            ->resources([
                TeacherResource::class,
                StudentResource::class,
                CourseResource::class,
            ])
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
