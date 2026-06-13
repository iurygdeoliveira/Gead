<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\AccountSuspended;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Auth\ResetPassword;
use App\Filament\Pages\Auth\VerificationPending;
use App\Http\Middleware\EnsureSecurityHeaders;
use App\Http\Middleware\RedirectToProperPanelMiddleware;
use Devletes\FilamentOrbitTheme\OrbitThemePlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Contracts\View\Factory;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AuthPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('auth')
            ->path('')
            ->default()
            //->darkMode(true, true)
            ->brandLogo(fn (): Factory|\Illuminate\Contracts\View\View => view('filament.auth.logo_auth'))
            ->brandLogoHeight('8rem')
            // ->viteTheme('resources/css/filament/admin/theme.css')
            ->authGuard('web')
            ->colors([
                'danger' => [
                    50 => '#fef2f2',
                    100 => '#fee2e2',
                    200 => '#fecaca',
                    300 => '#fca5a5',
                    400 => '#f87171',
                    500 => '#e7010a',
                    600 => '#e7010a',
                    700 => '#b91c1c',
                    800 => '#991b1b',
                    900 => '#7f1d1d',
                    950 => '#450a0a',
                ],
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                VerificationPending::class,
                AccountSuspended::class,
            ])
            ->login(Login::class)
            ->passwordReset(RequestPasswordReset::class, ResetPassword::class)
            ->multiFactorAuthentication(
                AppAuthentication::make()
                    ->recoverable()
            )
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
            ->plugin(OrbitThemePlugin::make()
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
            );

    }
}
