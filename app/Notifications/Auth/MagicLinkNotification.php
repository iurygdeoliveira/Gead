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
            ->view('emails.auth.magic-link', ['url' => $url]);
    }
}
