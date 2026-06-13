<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicLinkNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('magic.callback', ['token' => $this->token]);
        
        return (new MailMessage)
            ->subject('Acesso Seguro — GeAD')
            ->greeting('Acesso Seguro')
            ->line('Você solicitou acesso ao sistema GeAD via link mágico.')
            ->line('Clique no botão abaixo para entrar sem necessidade de senha.')
            ->action('Entrar com E-mail Institucional', $url)
            ->line('Este link é válido por 15 minutos. Se você não solicitou este acesso, pode ignorar este e-mail.');
    }
}
