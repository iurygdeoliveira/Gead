<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\MagicLoginToken;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendMagicLinkAction
{
    public function execute(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Usuário não encontrado com este e-mail.'
            ]);
        }

        if ($user->hasRole('admin')) { 
            throw ValidationException::withMessages([
                'email' => 'Contas administrativas exigem senha.'
            ]);
        }
        
        $plainToken = Str::random(64);
        
        MagicLoginToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => hash('sha256', $plainToken),
                'expires_at' => now()->addMinutes(15)
            ]
        );
        
        $user->notify(new MagicLinkNotification($plainToken));
    }
}
