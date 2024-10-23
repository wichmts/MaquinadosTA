<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restablecimiento de contraseña')
            ->line('Recibes este correo electrónico porque solicitamos un restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer contraseña', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('Si no realizaste esta solicitud, no se requiere realizar ninguna otra acción.');
    }
}
