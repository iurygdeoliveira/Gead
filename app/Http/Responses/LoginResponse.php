<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\Team;
use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as FilamentLoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements FilamentLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        $adminPanel = Filament::getPanel('admin');
        if ($adminPanel && $user->canAccessPanel($adminPanel)) {
            return redirect()->to('/admin');
        }

        $panels = ['manager', 'teacher', 'tae', 'student'];

        foreach ($panels as $panelName) {
            $panel = Filament::getPanel($panelName);
            if ($panel && $user->canAccessPanel($panel)) {
                /** @var Team|null $firstTeam */
                $firstTeam = $user->teams()->first();
                if ($firstTeam) {
                    return redirect()->to('/' . $panelName . '/' . $firstTeam->slug);
                }

                return redirect()->to('/' . $panelName);
            }
        }

        // Fallback para a rota home se nenhum role for encontrado
        return to_route('home');
    }
}
