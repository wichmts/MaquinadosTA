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