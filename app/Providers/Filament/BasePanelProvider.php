<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureSecurityHeaders;
use App\Http\Middleware\RedirectToProperPanelMiddleware;
use App\Filament\Configurators\FilamentComponentsConfigurator;
use Devletes\FilamentOrbitTheme\OrbitThemePlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

abstract class BasePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $this->applySharedPlugins(
            $panel
                ->id($this->getPanelId())
                ->path($this->getPanelPath())
                ->spa()
                ->globalSearch(false)
                ->databaseTransactions()
                ->profile()
                ->topbar(false)
                ->bootUsing(function (): void {
                    FilamentComponentsConfigurator::configure();
                })
               ->brandLogo(fn () => view('filament.logo'))
               ->brandLogoHeight('10rem')
               ->renderHook(
                   PanelsRenderHook::STYLES_AFTER,
                   fn (): HtmlString => new HtmlString('
                       <style>
                           .fi-sidebar-nav {
                               overflow-y: visible !important;
                               height: auto !important;
                           }
                           .fi-sidebar {
                               height: auto !important;
                               min-height: 100vh !important;
                               position: relative !important;
                           }
                       </style>
                   ')
               )
                ->multiFactorAuthentication(
                    AppAuthentication::make()
                        ->recoverable()
                )
                ->sidebarWidth('15rem')
                ->maxContentWidth(Width::Full)
                ->middleware([
                    EncryptCookies::class,
                    AddQueuedCookiesToResponse::class,
                    StartSession::class,
                    AuthenticateSession::class,
                    ShareErrorsFromSession::class,
                    PreventRequestForgery::class,
                    SubstituteBindings::class,
                    DisableBladeIconComponents::class,
                    DispatchServingFilamentEvent::class,
                    RedirectToProperPanelMiddleware::class,
                    EnsureSecurityHeaders::class,
                ])
                ->authMiddleware([
                    Authenticate::class,
                ])
        );
    }

    protected function applySharedPlugins(Panel $panel): Panel
    {
        return $panel
            ->plugin(
                OrbitThemePlugin::make()
                ->primaryColor([
                    50 => '#faffe5',
                    100 => '#f4ffc7',
                    200 => '#e5ff8a',
                    300 => '#d3ff42',
                    400 => '#d6ff33',
                    500 => '#ccff03',
                    600 => '#ccff03',
                    700 => '#b3e600',
                    800 => '#748c00',
                    900 => '#475900',
                    950 => '#2b3600',
                ])
                ->dateWeatherWidget()
            );
    }

    abstract protected function getPanelId(): string;

    abstract protected function getPanelPath(): string;
}
