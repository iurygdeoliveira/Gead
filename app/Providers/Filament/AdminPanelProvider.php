<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\EvaluationInsightsPage;
use App\Filament\Pages\LoginAuditPage;
use App\Filament\Pages\Mail\PreviewTemplate;
use App\Filament\Pages\Mail\Templates;
use Filament\Panel;
use WallaceMartinss\FilamentSecurity\FilamentSecurityPlugin;

class AdminPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        // Particularidades do painel admin
        $panel = $panel
            ->plugin(
                FilamentSecurityPlugin::make()
                    ->disposableEmailProtection()
                    ->honeypotProtection()
                    ->cloudflareBlocking()
                    ->eventLog(false)
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                AdminDashboard::class,
                LoginAuditPage::class,
                Templates::class,
                PreviewTemplate::class,
                EvaluationInsightsPage::class,
            ])
            ->tenant(null);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'admin';
    }

    protected function getPanelPath(): string
    {
        return 'admin';
    }
}
