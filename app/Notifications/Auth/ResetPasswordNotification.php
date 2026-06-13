<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * The password reset URL.
     *
     * @var string
     */
    public $url;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    #[\Override]
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notificação de Redefinição de Senha')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.')
            ->action('Modificar Senha', $this->url)
            ->line('Este link de redefinição de senha expirará em ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.')
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional será necessária.')
            ->line('Saudações,')
            ->line('LabSIS-KIT');
    }
}
