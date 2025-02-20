<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePruebasDisenoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pruebas_diseno', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();

            $table->boolean('liberada')->nullable();
            $table->string('fecha_inicio')->nullable();
            $table->string('fecha_liberada')->nullable();

            $table->text('descripcion')->nullable();
            $table->text('involucrados')->nullable();
            $table->text('herramienta_medicion')->nullable();
            $table->text('hallazgos')->nullable();
            $table->text('plan_accion')->nullable();
            $table->text('checklist')->nullable();
            $table->string('archivo_dimensional')->nullable();
            $table->string('altura_cierre')->nullable();
            $table->string('altura_medidas')->nullable();
            
            $table->integer('herramental_id')->nullable();
            $table->integer('usuario_id')->nullable();
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
        Schema::dropIfExists('pruebas_diseno');
    }
}
