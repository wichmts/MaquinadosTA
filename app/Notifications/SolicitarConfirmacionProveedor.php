<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitarConfirmacionProveedor extends Notification
{
    use Queueable;
     private $cotizacion;
     private $propuesta;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
   public function __construct($cotizacion, $propuesta)
    {
        $this->cotizacion = $cotizacion;
        $this->propuesta = $propuesta;
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
        ->subject('Confirmación de servicio de transporte')
        ->markdown('emails.solicitud_confirmacion_proveedor', [
            'notifiable' => $notifiable,
            'cotizacion' => $this->cotizacion,
            'propuesta' => $this->propuesta,
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
