<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\RoleType;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;

        if ($event->user instanceof \App\Models\User) {
            if (! $event->user->hasRole(RoleType::ADMIN->value)) {
                $audit = $event->user->auditEvent('failed_login');
                if ($audit && $email) {
                    $audit->new_values = array_merge($audit->new_values ?? [], ['email' => $email]);
                    $audit->save();
                }
            }
        } elseif ($email) {
            // Log manually if user doesn't exist
            \OwenIt\Auditing\Models\Audit::create([
                'event' => 'failed_login',
                'auditable_type' => \App\Models\User::class,
                'auditable_id' => 0, // Ou null se o seu banco permitir
                'old_values' => [],
                'new_values' => ['email' => $email],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
