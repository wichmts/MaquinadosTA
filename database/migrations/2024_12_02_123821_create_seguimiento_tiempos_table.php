<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeguimientoTiemposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seguimiento_tiempos', function (Blueprint $table) {
            $table->id();
            $table->string('accion_id');       //  corte, programacion
            $table->string('accion');       //  corte, programacion
            $table->boolean('tipo');         //  activo, inactivo
            $table->string('fecha');
            $table->string('hora');
            $table->text('motivo')->nullable();
            $table->integer('componente_id');
            $table->integer('usuario_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seguimiento_tiempos');
    }
}
