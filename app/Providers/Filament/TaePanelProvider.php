<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\ChangePassword;
use App\Filament\Pages\LoginAuditPage;
use App\Filament\Pages\TaeDashboard;
use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Team;
use Filament\Panel;

class TaePanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        $panel = parent::panel($panel);

        $panel = $panel
            ->tenant(Team::class, slugAttribute: 'slug', ownershipRelationship: 'teams')
            ->tenantMenu(false)
            ->resources([
                TeacherResource::class,
                StudentResource::class,
                CourseResource::class,
                EvaluationResource::class,
                CourseClassResource::class,
            ])
            ->pages([
                TaeDashboard::class,
                LoginAuditPage::class,
                ChangePassword::class,
            ])
            ->tenantMiddleware([
                TeamSyncMiddleware::class,
                EnsurePasswordIsChanged::class,
            ], isPersistent: true);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'tae';
    }

    protected function getPanelPath(): string
    {
        return 'tae';
    }
}
