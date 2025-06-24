<?php

namespace App\Jobs;

use App\Notificacion;
use App\User;
use App\Mail\NotificacionGenerica;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable; // <-- este es clave
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarCorreoNotificacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificacion;

    public function __construct(Notificacion $notificacion)
    {
        $this->notificacion = $notificacion;
    }

    public function handle()
    {
        $usuarios = collect();

        if (!empty($this->notificacion->responsables)) {
            $responsables = json_decode($this->notificacion->responsables, true);
            $usuarios = User::whereIn('id', $responsables)->get();
        } else {
            $roles = json_decode($this->notificacion->roles, true);
            $usuarios = User::whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })->get();
        }

        $usuarios
        ->unique('email')
        ->filter(function ($usuario) {
            $email = trim($usuario->email);
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        })
        ->each(function ($usuario) {
            try {
                Mail::to($usuario->email)->send(
                    new NotificacionGenerica($this->notificacion)
                );
            } catch (\Throwable $e) {
                \Log::warning("Fallo al enviar correo a {$usuario->email}: " . $e->getMessage());
            }
        });

    }
}
