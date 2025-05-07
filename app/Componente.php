<?php

namespace App;

use App\SeguimientoTiempo;
use Carbon\Carbon;
use App\Maquina;
use App\Herramental;
use App\Anio;
use App\Proyecto;
use App\Cliente;
use App\Solicitud;
use App\SolciitudExterna;
use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    public function material(){
        return $this->belongsTo(Material::class);
    }
    public function herramental(){
        return $this->belongsTo(Herramental::class);
    }
    public function fabricaciones(){
        return $this->hasMany(Fabricacion::class);
    }
    public function refabricaciones() {
        $refabricaciones = Componente::where('herramental_id', $this->herramental_id)
            ->where('nombre', $this->nombre)
            ->where('cargado', true)
            ->where('es_compra', false)
            ->orderBy('version', 'desc')
            ->get(['id', 'version']) // Obtener solo los campos necesarios
            ->map(function($componente) {
                return [
                    'id' => $componente->id,
                    'creada' => $componente->fecha_cargado,
                    'version' => $componente->version
                ];
            })
            ->toArray(); // Convertir la colección a un array

        return $refabricaciones; // Devuelve el array con los componentes
    }
    public function maquinas(){
        $maquinas = Maquina::select('maquinas.id as maquina_id','maquinas.requiere_programa', 'maquinas.nombre', 'fabricaciones.id as documento_id', 'fabricaciones.archivo as documento_nombre')
            ->leftJoin('fabricaciones', function($join) {
                $join->on('fabricaciones.maquina_id', '=', 'maquinas.id');
            })
            ->where('fabricaciones.componente_id', $this->id)
            ->get()
            ->groupBy('maquina_id');

        $resultado = $maquinas->map(function ($maquina) {
            $archivos = $maquina->map(function ($fabricacion) {
                return [
                    'id' => $fabricacion->documento_id,
                    'nombre' => $fabricacion->documento_nombre,
                    'tamano' => $fabricacion->documento_tamano,
                ];
            });

            if ($archivos->isEmpty()) {
                $archivos = [];
            }

            return [
                'maquina_id' => $maquina[0]->maquina_id,
                'nombre' => $maquina[0]->nombre,
                'requiere_programa' => $maquina[0]->requiere_programa,
                'archivos' => $archivos,
            ];
        });
        return $resultado->values()->all();
    }
    public static function procesosFijos(){
        return [
            ['id' => 1, 'prioridad' => 1, 'nombre' => 'Cortar'],
            ['id' => 2, 'prioridad' => 2, 'nombre' => 'Programar'],
            ['id' => 3, 'prioridad' => 3, 'nombre' => 'Maquinar'],
            ['id' => 4, 'prioridad' => 4, 'nombre' => 'Tornear'],
            ['id' => 5, 'prioridad' => 5, 'nombre' => 'Roscar/Rebabear'],
            ['id' => 6, 'prioridad' => 6, 'nombre' => 'Templar'],
            ['id' => 7, 'prioridad' => 7, 'nombre' => 'Rectificar'],
            ['id' => 8, 'prioridad' => 8, 'nombre' => 'EDM'],
        ];
    }
    public function rutaAvance(){
        $procesos = $this->procesosFijos(); // Obtén los procesos fijos
        $resultados = [];

        foreach ($procesos as $proceso) {
            // Filtra los registros de seguimiento para el proceso actual
            $seguimientos = SeguimientoTiempo::where('componente_id', $this->id)
                ->where('accion_id', $proceso['id'])
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get();

            $totalHoras = 0;
            $totalMinutos = 0;
            $ultimoInicio = null; // Variable para guardar el último 'true' (inicio)

            foreach ($seguimientos as $key => $registro) {
                if ($registro->tipo) { // Tipo == 1 -> Inicio
                    // Guardar el inicio si es el primer 'true' o cuando el proceso comienza de nuevo
                    $inicio = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);
                    $ultimoInicio = $inicio;
                } else { // Tipo == 0 -> Pausa
                    // Si ya tenemos un inicio, calculamos la diferencia
                    if ($ultimoInicio) {
                        $fin = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);

                        $diferencia = $inicio->diff($fin);
                        $totalHoras += $diferencia->h;
                        $totalMinutos += $diferencia->i;

                        // Asegurarse de que los minutos se conviertan en horas si es necesario
                        if ($totalMinutos >= 60) {
                            $totalHoras += floor($totalMinutos / 60);
                            $totalMinutos = $totalMinutos % 60;
                        }
                        // Resetear el inicio después de la pausa
                        $ultimoInicio = null;
                    }
                }
            }

            // Si el último tipo es 'true', se calcula el tiempo desde ese punto hasta el momento actual
            if ($ultimoInicio) {
                $ahora = Carbon::now(); // Hora y fecha actuales
                $diferencia = $ultimoInicio->diff($ahora);
                $totalHoras += $diferencia->h;
                $totalMinutos += $diferencia->i;

                // Asegurarse de que los minutos se conviertan en horas si es necesario
                if ($totalMinutos >= 60) {
                    $totalHoras += floor($totalMinutos / 60);
                    $totalMinutos = $totalMinutos % 60;
                }
            }

            $resultados[] = [
                'id' => $proceso['id'],
                'name' => $proceso['nombre'],
                'time' => [
                    [
                        'hora_inicio' => 1,
                        'minuto_inicio' => 0,
                        'horas' => $totalHoras,
                        'minutos' => $totalMinutos,
                        'type' => 'normal',
                    ]
                ]
            ];
        }

        return $resultados;
    }
    public function tiempoCorte(){
        $registros = SeguimientoTiempo::where('componente_id', $this->id)->where('accion', 'corte')->orderBy('fecha')->orderBy('hora')->get();
        $totalSegundos = 0;
        $ultimoInicio = null;

        foreach ($registros as $registro) {
            if ($registro->tipo == 1) {
                $ultimoInicio = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);
            } elseif ($registro->tipo == 0 && $ultimoInicio) {
                $fin = Carbon::createFromFormat('Y-m-d H:i', $registro->fecha . ' ' . $registro->hora);
                $totalSegundos += $ultimoInicio->diffInSeconds($fin);
                $ultimoInicio = null;
            }
        }
        $horas = floor($totalSegundos / 3600);
        $minutos = floor(($totalSegundos % 3600) / 60);
        
        return [
            'horas' => $horas,
            'minutos' => $minutos,
        ];
    }
    public function esComponenteExterno(){
        return SolicitudExterna::where('componente_id', $this->id)->exists();
    }
    public function tieneRetrasos(){
        return $this->fabricaciones()->whereNotNull('motivo_retraso')->exists();
    }
    public function tieneRefabricaciones() {
        return Componente::where('herramental_id', $this->herramental_id)
            ->where('nombre', $this->nombre)
            ->where('cargado', true)
            ->where('es_compra', false)
            ->count() > 1;
    }
    public function tieneRetrabajos(){
        $ruta = json_decode($this->ruta, true); // Convertir JSON a array

        foreach ($ruta as $proceso) {
            foreach ($proceso['time'] as $tiempo) {
                if ($tiempo['type'] === 'rework') {
                    return true; // Si encuentra un retrabajo, devuelve true
                }
            }
        }

        return false; // No hay retrabajos
    }
    public function tieneSolicitudes($tipo){
       return Solicitud::where('componente_id', $this->id)
            ->where('tipo', $tipo)
            ->where(function ($query) {
                $query->where('atendida', '!=', true)
                    ->orWhereNull('atendida');
            })
            ->exists();
    }
    public function toArray(){
  		$data = parent::toArray();
        $data['material_nombre'] = $this->material ? $this->material->nombre : '';
        $data['archivo_2d_public'] = $this->archivo_2d ? $this->herramental->proyecto_id .'/' . $this->herramental->id . '/componentes/'. $this->archivo_2d : '';
        $data['archivo_3d_public'] = $this->archivo_3d ? $this->herramental->proyecto_id .'/' . $this->herramental->id . '/componentes/'. $this->archivo_3d : '';
        $data['archivo_explosionado_public'] = $this->herramental->archivo_explosionado ? $this->herramental->proyecto_id .'/' . $this->herramental->id . '/componentes/'. $this->herramental->archivo_explosionado : '';
        $data['ruta'] = $this->ruta ? json_decode($this->ruta, true) : [];
        $data['fabricaciones'] = $this->fabricaciones ? $this->fabricaciones : [];
        $data['rutaAvance'] = $this->rutaAvance();
        $data['maquinas'] = $this->maquinas();
        $data['esComponenteExterno'] = $this->esComponenteExterno();
        $data['refabricaciones'] = $this->refabricaciones();
        $data['tieneRetrasos'] = $this->tieneRetrasos();
        $data['tieneRetrabajos'] = $this->tieneRetrabajos();
        $data['tieneRefabricaciones'] = $this->tieneRefabricaciones();

        $data['archivo_2d_show'] = $this->archivo_2d ? preg_replace('/^[^_]+_/', '', $this->archivo_2d) : null;
        $data['archivo_3d_show'] = $this->archivo_3d ? preg_replace('/^[^_]+_/', '', $this->archivo_3d) : null;
        $data['archivo_explosionado_show'] = $this->herramental->archivo_explosionado ? preg_replace('/^[^_]+_/', '', $this->herramental->archivo_explosionado) : null;

        $herramental = Herramental::findOrFail($this->herramental_id);
        $proyecto = Proyecto::findOrFail($herramental->proyecto_id);
        $cliente = Cliente::findOrFail($proyecto->cliente_id);
        $anio = Anio::findOrFail($cliente->anio_id);
        $data['rutaComponente'] = "?a={$anio->id}&c={$cliente->id}&p={$proyecto->id}&h={$herramental->id}&co={$this->id}";

        $data['bAjuste'] = $this->tieneSolicitudes('ajuste');
        $data['bRetrabajo'] = $this->tieneSolicitudes('retrabajo');
        $data['bModificacion'] = $this->tieneSolicitudes('modificacion');
        $data['bRefabricacion'] = $this->tieneSolicitudes('refabricacion');
        $data['bRechazo'] = $this->tieneSolicitudes('rechazo');

        return $data;
    }

}
