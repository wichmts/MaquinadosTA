<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fabricaciones', function (Blueprint $table) {
            $table->id();
            $table->integer('maquina_id')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->integer('componente_id')->nullable();
            $table->string('archivo')->nullable();
            $table->string('estatus_fabricacion')->nullable();
            $table->boolean('fabricado')->nullable();
            $table->text('comentarios_terminado')->nullable();
            $table->text('registro_medidas')->nullable();
            $table->string('foto')->nullable();
            $table->text('motivo_retraso')->nullable();
            
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
        Schema::dropIfExists('fabricaciones');
    }
}
