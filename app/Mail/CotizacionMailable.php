<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CotizacionMailable extends Mailable
{
    use Queueable, SerializesModels;

    private $referencia;
    private $confirmarUrl;
    private $declinarUrl;
    private $nombre_cliente;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($referencia, $confirmarUrl, $nombre_cliente)
    {
        $this->nombre_cliente = $nombre_cliente;
        $this->referencia = $referencia;
        $this->confirmarUrl = $confirmarUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.propuesta_cliente')
            ->subject('Propuesta de cotización')
            ->with([
                'nombre_cliente' => $this->nombre_cliente,
                'referencia' => $this->referencia,
                'confirmarUrl' => $this->confirmarUrl,
                'logo' => asset('/paper/img/logo-color1.png')
            ]);
    }
}