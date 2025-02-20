<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePruebasProcesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pruebas_proceso', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();

            $table->boolean('liberada')->nullable();
            $table->string('fecha_inicio')->nullable();
            $table->string('fecha_liberada')->nullable();

            
            $table->string('archivo')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('lista_refacciones')->nullable();
            $table->boolean('kit_conversion')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('comentarios')->nullable();
            $table->text('plan_accion')->nullable();

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
        Schema::dropIfExists('pruebas_proceso');
    }
}
