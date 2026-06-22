<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        if ($user && $user->must_change_password) {
            $panelId = Filament::getCurrentPanel()?->getId();

            if ($panelId) {
                // Ensure we are not already on the change password page or livewire endpoints
                $changePasswordRoute = "filament.{$panelId}.pages.change-password";
                $isLivewire = $request->routeIs('livewire.*') || $request->is('livewire/*');

                if (! $request->routeIs($changePasswordRoute) && ! $isLivewire) {
                    $tenant = Filament::getTenant();
                    return redirect()->route($changePasswordRoute, $tenant ? ['tenant' => $tenant] : []);
                }
            }
        }

        return $next($request);
    }
}
