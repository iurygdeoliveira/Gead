<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Traits\Filament\NotificationsTrait;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Password;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    use NotificationsTrait;

    #[\Override]
    public function getHeading(): string|Htmlable
    {
        return 'Esqueceu sua senha?';
    }

    #[\Override]
    public function getSubheading(): string|Htmlable|null
    {
        return 'Digite seu email e enviaremos um link para redefinir sua senha.';
    }

    #[\Override]
    protected function getSentNotification(string $status): ?Notification
    {
        return $this->buildNotification(
            type: 'primary',
            title: 'Email enviado',
            body: 'Verifique sua caixa de entrada para redefinir sua senha.',
            seconds: 8
        );
    }

    #[\Override]
    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return;
        }

        $data = $this->form->getState();

        $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            $this->getCredentialsFromFormData($data),
            function (CanResetPassword $user, string $token): void {
                if (
                    ($user instanceof FilamentUser) &&
                    (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel()))
                ) {
                    return;
                }

                if (! $user instanceof User) {
                    $userClass = $user::class;
                    throw new \LogicException("User [{$userClass}] is not an instance of App\Models\User.");
                }

                $notification = new ResetPasswordNotification($token);
                $notification->url = Filament::getResetPasswordUrl($token, $user);

                $user->notify($notification);

                if (class_exists(PasswordResetLinkSent::class)) {
                    event(new PasswordResetLinkSent($user));
                }
            },
        );

        if ($status !== Password::RESET_LINK_SENT) {
            $this->getFailureNotification($status)?->send();

            return;
        }

        $this->getSentNotification($status)?->send();

        $this->form->fill();
    }
}
