<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\MagicLoginToken;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendMagicLinkAction
{
    /**
     * Máximo de solicitações de link mágico por e-mail em 15 minutos.
     */
    private const int MAX_ATTEMPTS_PER_EMAIL = 5;

    /**
     * Máximo de solicitações de link mágico por IP em 15 minutos.
     */
    private const int MAX_ATTEMPTS_PER_IP = 10;

    /**
     * Janela de tempo em segundos (15 minutos).
     */
    private const int DECAY_SECONDS = 900;

    public function execute(string $email): void
    {
        $this->enforceRateLimits($email);

        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Usuário não encontrado com este e-mail.',
            ]);
        }

        if ($user->hasRole('admin')) {
            throw ValidationException::withMessages([
                'email' => 'Contas administrativas exigem senha.',
            ]);
        }

        $plainToken = Str::random(64);

        MagicLoginToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => hash('sha256', $plainToken),
                'expires_at' => now()->addMinutes(15),
            ]
        );

        $user->notify(new MagicLinkNotification($plainToken));
    }

    /**
     * Aplica rate limit por e-mail e por IP para prevenir abuso (RSK-06).
     *
     * @throws ValidationException
     */
    private function enforceRateLimits(string $email): void
    {
        $emailKey = 'magic-link-email:' . Str::lower($email);
        $ipKey = 'magic-link-ip:' . (request()->ip() ?? 'unknown');

        if (RateLimiter::tooManyAttempts($emailKey, self::MAX_ATTEMPTS_PER_EMAIL)) {
            $seconds = RateLimiter::availableIn($emailKey);
            $minutes = (int) ceil($seconds / 60);

            throw ValidationException::withMessages([
                'email' => "Muitas solicitações para este e-mail. Tente novamente em {$minutes} minuto(s).",
            ]);
        }

        if (RateLimiter::tooManyAttempts($ipKey, self::MAX_ATTEMPTS_PER_IP)) {
            $seconds = RateLimiter::availableIn($ipKey);
            $minutes = (int) ceil($seconds / 60);

            throw ValidationException::withMessages([
                'email' => "Muitas solicitações deste endereço. Tente novamente em {$minutes} minuto(s).",
            ]);
        }

        RateLimiter::hit($emailKey, self::DECAY_SECONDS);
        RateLimiter::hit($ipKey, self::DECAY_SECONDS);
    }
}
