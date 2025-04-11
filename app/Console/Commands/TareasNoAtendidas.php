<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Solicitud;
use App\Notificacion;
use App\User;
use App\Componente;
use App\Herramental;
use App\Cliente;
use App\Anio;
use App\Proyecto;

class TareasNoAtendidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solicitudes:notificacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera una notificación cada 24 horas para el jefe de área sobre solicitudes no atendidas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $solicitudes = Solicitud::where('atendida', false)->orWhereNull('atendida')->get();
        $solicitudes = Solicitud::where('atendida', false)
            ->orWhereNull('atendida')
            ->select('componente_id')
            ->groupBy('componente_id')
            ->get();

        if ($solicitudes->isEmpty()) {
            $this->info('No hay solicitudes pendientes.');
            return;
        }

        foreach ($solicitudes as $solicitud) {
            $componente = Componente::find($solicitud->componente_id);
            if (!$componente || ($componente && $componente->refabricado)) continue;

            $herramental = Herramental::find($componente->herramental_id);
            if (!$herramental) continue;

            $proyecto = Proyecto::find($herramental->proyecto_id);
            if (!$proyecto) continue;

            $cliente = Cliente::find($proyecto->cliente_id);
            if (!$cliente) continue;

            $anio = Anio::find($cliente->anio_id);
            if (!$anio) continue;

            $notificacion = new Notificacion();
            $notificacion->roles = json_encode(['JEFE DE AREA']);
            $notificacion->url_base = '/enrutador';
            $notificacion->anio_id = $anio->id;
            $notificacion->cliente_id = $cliente->id;
            $notificacion->proyecto_id = $proyecto->id;
            $notificacion->herramental_id = $herramental->id;
            $notificacion->componente_id = $componente->id;
            $notificacion->cantidad = $componente->cantidad;
            $notificacion->descripcion = 'TIENES SOLICITUDES SIN ATENDER PARA ESTE COMPONENTE.';
            $notificacion->save();
        }
        $usuarios = User::role('JEFE DE AREA')->get();
        foreach ($usuarios as $usuario) {
            $usuario->hay_notificaciones = true;
            $usuario->save();
        }   
        $this->info('Notificaciones generadas con éxito.');
    }
}
