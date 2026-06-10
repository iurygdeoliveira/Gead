<?php

declare(strict_types = 1);

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        // Only log if the user exists (invalid credentials)
        // If user is null, it means the email doesn't exist
        if ($event->user && ! $event->user->hasRole(\App\Enums\RoleType::ADMIN->value)) {
            $event->user->auditEvent('failed_login');
        }
    }
}
