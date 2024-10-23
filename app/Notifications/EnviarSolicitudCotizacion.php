<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnviarSolicitudCotizacion extends Notification
{
    use Queueable;
    private $cotizacion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($cotizacion)
    {
        $this->cotizacion = $cotizacion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('Nueva solicitud de cotización')
        ->markdown('emails.solicitud_cotizacion', [
            'notifiable' => $notifiable,
            'cotizacion' => $this->cotizacion,
            'logo' => asset('paper/img/logo-color1.png'),
            'url' =>  url()->to('/') . '/solicitudes-cotizaciones'
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
