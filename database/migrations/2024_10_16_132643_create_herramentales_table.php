<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHerramentalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('herramentales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('archivo')->nullable();
            $table->string('archivo2')->nullable();
            $table->integer('proyecto_id');
            $table->string('estatus_ensamble')->nullable();     //inicial, checklist, proceso, finalizado
            $table->string('estatus_pruebas_diseno')->nullable();      //inicial, proceso, finalizada
            $table->string('estatus_pruebas_proceso')->nullable();      //inicial, proceso, finalizada

            $table->string('inicio_ensamble')->nullable();
            $table->string('termino_ensamble')->nullable();

            $table->text('checklist')->nullable();
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
        Schema::dropIfExists('herramentales');
    }
}
