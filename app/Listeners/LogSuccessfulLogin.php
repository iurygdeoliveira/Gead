<?php

declare(strict_types = 1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (! $event->user->hasRole(\App\Enums\RoleType::ADMIN->value)) {
            $event->user->auditEvent('login');
        }
    }
}
