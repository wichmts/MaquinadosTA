<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;

class AddUuidToRutaInComponentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('componentes')->orderBy('id')->chunk(100, function ($componentes) {
            foreach ($componentes as $componente) {
                $ruta = $componente->ruta;
                if (empty($ruta)) {
                    continue;
                }
                $procesos = json_decode($ruta, true);
                if (!is_array($procesos) || empty($procesos)) {
                    continue;
                }
                if (isset($procesos[0]['uuid'])) {
                    continue;
                }
                $nuevosProcesos = [];
                foreach ($procesos as $proceso) {
                    $nuevosProcesos[] = array_merge(['uuid' => (string) Str::uuid()], $proceso);
                }
                $nuevaRutaJson = json_encode($nuevosProcesos);
                DB::table('componentes')
                    ->where('id', $componente->id)
                    ->update(['ruta' => $nuevaRutaJson]);
            }
        });
        
        //Extender el UUID a las 'fabricaciones' asociadas para evitar errores.
        DB::table('fabricaciones')->orderBy('id')->chunk(100, function ($fabricaciones) {
            $maquinasCache = [];
            $rutasCache = [];

            foreach ($fabricaciones as $fabricacion) {
                $maquina = $this->getMaquinaTipoProceso($fabricacion->maquina_id, $maquinasCache);
                if (!$maquina) {
                    continue; 
                }
                $tipoProceso = $maquina->tipo_proceso;
                $componente = $this->getComponenteRuta($fabricacion->componente_id, $rutasCache);
                if (!$componente || empty($componente->ruta)) {
                    continue; 
                }
                $procesos = json_decode($componente->ruta, true);
                if (!is_array($procesos)) {
                    continue;
                }
                $procesoUuid = null;
                foreach ($procesos as $proceso) {
                    if (isset($proceso['id']) && $proceso['id'] == $tipoProceso && isset($proceso['uuid'])) {
                        $procesoUuid = $proceso['uuid'];
                        break;
                    }
                }
                if ($procesoUuid) {
                    DB::table('fabricaciones')
                        ->where('id', $fabricacion->id)
                        ->update(['proceso_uuid' => $procesoUuid]);
                }
            }
        });

        // Actualizar la tabla 'SeguimientoTiempo'
        DB::table('seguimiento_tiempos')->orderBy('id')->chunk(100, function ($seguimientos) {
            $maquinasCache = [];
            $rutasCache = [];
            $fabricacionesCache = [];

          foreach ($seguimientos as $seguimiento) {
                $accion = strtolower($seguimiento->accion);
                $fabricacionId = $seguimiento->fabricacion_id;
                $nuevoAccionId = null;

                $componenteId = null;
                $maquinaId = null;

                if (!empty($fabricacionId)) {
                    $fabricacion = $this->getFabricacion($fabricacionId, $fabricacionesCache);
                    if ($fabricacion) {
                        $componenteId = $fabricacion->componente_id;
                        $maquinaId = $fabricacion->maquina_id;
                    }
                } else {
                    if (isset($seguimiento->componente_id)) {
                        $componenteId = $seguimiento->componente_id;
                    } else {
                        continue; 
                    }
                }
                if (!$componenteId) {
                    continue;
                }

                if ($accion === 'ensamble') {
                    $nuevoAccionId = '0';

                } elseif (in_array($accion, ['corte', 'programacion', 'fabricacion'])) {
                    
                    $componente = $this->getComponenteRuta($componenteId, $rutasCache);
                    if (!$componente || empty($componente->ruta)) {
                        continue;
                    }
                    $procesos = json_decode($componente->ruta, true);
                    if (!is_array($procesos)) {
                        continue;
                    }

                    if ($accion === 'corte') {
                        $nuevoAccionId = $this->findUuidByName($procesos, "Cortar");

                    } elseif ($accion === 'programacion') {
                        $nuevoAccionId = $this->findUuidByName($procesos, "Programar");

                    } elseif ($accion === 'fabricacion') {
                        if ($maquinaId) {
                            $maquina = $this->getMaquinaTipoProceso($maquinaId, $maquinasCache);
                            if ($maquina) {
                                $tipoProcesoId = $maquina->tipo_proceso;
                                $nuevoAccionId = $this->findUuidById($procesos, $tipoProcesoId);
                            }
                        }
                    }
                }
                
                if ($nuevoAccionId !== null) {
                    DB::table('seguimiento_tiempos')
                        ->where('id', $seguimiento->id)
                        ->update(['accion_id' => $nuevoAccionId]);
                }
            }
        });
    }

    /**
     * Helper para obtener una Fabricacion usando caché.
     */
    protected function getFabricacion($fabricacionId, &$cache)
    {
        if (!isset($cache[$fabricacionId])) {
            $cache[$fabricacionId] = DB::table('fabricaciones')
                ->select('componente_id', 'maquina_id')
                ->where('id', $fabricacionId)
                ->first();
        }
        return $cache[$fabricacionId];
    }


     // obtiene el uuid del proceso $tipo de un componente.
    protected function findUuidByName($procesos, $tipo){
        if (!is_array($procesos)) {
            return null;
        }
        foreach ($procesos as $proceso) {
            if (isset($proceso['name']) && strcasecmp($proceso['name'], $tipo) === 0) {
                return $proceso['uuid'] ?? null;
            }
        }
        return null;
    }

    /**
     * Busca un UUID dentro de los procesos por el campo 'id' (tipo_proceso).
     */
    protected function findUuidById(array $procesos, int $id): ?string
    {
        foreach ($procesos as $proceso) {
            if (isset($proceso['id']) && $proceso['id'] == $id && isset($proceso['uuid'])) {
                return $proceso['uuid'];
            }
        }
        return null;
    }

    protected function getMaquinaTipoProceso($maquinaId, &$cache)
    {
        if (!isset($cache[$maquinaId])) {
            $cache[$maquinaId] = DB::table('maquinas')
                ->select('tipo_proceso')
                ->where('id', $maquinaId)
                ->first();
        }
        return $cache[$maquinaId];
    }

    protected function getComponenteRuta($componenteId, &$cache)
    {
        if (!isset($cache[$componenteId])) {
            $cache[$componenteId] = DB::table('componentes')
                ->select('ruta')
                ->where('id', $componenteId)
                ->first();
        }
        return $cache[$componenteId];
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('componentes')->orderBy('id')->chunk(100, function ($componentes) {
            foreach ($componentes as $componente) {
                $ruta = $componente->ruta;

                if (empty($ruta)) {
                    continue;
                }

                $procesos = json_decode($ruta, true);

                if (!is_array($procesos) || empty($procesos) || !isset($procesos[0]['uuid'])) {
                    continue;
                }

                $procesosAntiguos = [];
                foreach ($procesos as $proceso) {
                    unset($proceso['uuid']);
                    $procesosAntiguos[] = $proceso;
                }

                $antiguaRutaJson = json_encode($procesosAntiguos);

                DB::table('componentes')
                    ->where('id', $componente->id)
                    ->update(['ruta' => $antiguaRutaJson]);
            }
        });
    }
};