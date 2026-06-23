<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Filament\Notifications\Notification;

trait NotificationsTrait
{
    public function notifySuccess(string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): void
    {
        $this->buildNotification('primary', $title, $body, $seconds, $persistent)->send();
    }

    public function notifyDanger(string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): void
    {
        $this->buildNotification('danger', $title, $body, $seconds, $persistent)->send();
    }

    public function notifyWarning(string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): void
    {
        $this->buildNotification('warning', $title, $body, $seconds, $persistent)->send();
    }

    protected function buildNotification(string $type, string $title, ?string $body = null, int $seconds = 8, bool $persistent = false): Notification
    {
        $notification = Notification::make()->title($title);

        if ($body !== null) {
            $notification->body($body);
        }

        match ($type) {
            'primary' => $notification->success()
                ->id('custom-primary-' . \Illuminate\Support\Str::uuid())
                ->icon('heroicon-s-check-circle')
                ->iconColor('#2b3600') // Ícone na cor escura
                ->persistent()
                ->duration(500)
                ->color([
                    // Fundo no Light Mode (50, 100)
                    50 => '#d3ff42',
                    100 => '#ccff03',
                    200 => '#b3e600',
                    300 => '#84a100',
                    // Textos e Bordas (400 a 700) - Cores Escuras
                    400 => '#475900',
                    500 => '#2b3600',
                    600 => '#2b3600',
                    700 => '#1a2100',
                    800 => '#ccff03',
                    // Fundo no Dark Mode (900, 950) - Cores Claras
                    900 => '#ccff03',
                    950 => '#d3ff42',
                ]),

            'danger' => $notification->danger()
                ->icon('heroicon-c-no-symbol')
                ->iconColor('danger')
                ->color('danger'),

            'warning' => $notification->warning()
                ->icon('heroicon-s-exclamation-triangle')
                ->iconColor('warning')
                ->color('warning'),

            default => $notification->info(),
        };

        if ($persistent) {
            $notification->persistent();
        } else {
            $notification->seconds($seconds);
        }

        return $notification;
    }
}
