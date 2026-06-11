<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateMagicLinkAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

class MagicLinkController
{
    public function callback(string $token, AuthenticateMagicLinkAction $action): RedirectResponse
    {
        try {
            $action->execute($token);
            
            return redirect()->route('filament.admin.pages.dashboard');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('filament.admin.auth.login')
                ->withErrors(['email' => 'Link inválido ou expirado.']);
        }
    }
}
