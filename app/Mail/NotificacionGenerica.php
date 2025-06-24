<?php

namespace App\Mail;

use App\Notificacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionGenerica extends Mailable
{
    use Queueable, SerializesModels;

    public $notificacion;
    public $url;

    public function __construct(Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;
        $this->url = $this->generarUrl();
        $this->datosExtra = [
            'componente' => $notificacion->componente ? $notificacion->componente->nombre : null,
            'herramental' => $notificacion->herramental ? $notificacion->herramental->nombre : null,
            'proyecto' => $notificacion->proyecto ? $notificacion->proyecto->nombre : null,
            'cliente' => $notificacion->cliente ? $notificacion->cliente->nombre : null,
            'anio' => $notificacion->anio ? $notificacion->anio->nombre : null,
            'fecha' => $notificacion->created_at->isoFormat('D/M/YYYY'),
            'hora' => $notificacion->created_at->isoFormat('h:mm a'),
        ];
    }

    public function build()
    {
        return $this->subject($this->notificacion->descripcion)
                    ->markdown('emails.notificacion')
                    ->with([
                        'descripcion' => $this->notificacion->descripcion,
                        'url' => $this->url,
                        'datosExtra' => $this->datosExtra,
                    ]);
    }

    private function generarUrl()
    {
        $base = $this->notificacion->url_base;

        if (in_array('OPERADOR', json_decode($this->notificacion->roles, true))) {
            return url("{$base}?maq={$this->notificacion->maquina_id}&co={$this->notificacion->componente_id}&fab={$this->notificacion->fabricacion_id}");
        }

        return url("{$base}?a={$this->notificacion->anio_id}&c={$this->notificacion->cliente_id}&p={$this->notificacion->proyecto_id}&h={$this->notificacion->herramental_id}&co={$this->notificacion->componente_id}");
    }
}
