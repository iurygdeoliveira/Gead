<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\RoleType;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if (! $event->user->hasRole(RoleType::ADMIN->value)) {
            $event->user->auditEvent('logout');
        }
    }
}
