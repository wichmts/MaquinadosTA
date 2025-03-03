<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesExternasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_externas', function (Blueprint $table) {
            $table->id();
            $table->string("fecha_solicitud")->nullable();
            $table->string("fecha_deseada_entrega")->nullable();
            $table->string("fecha_real_entrega")->nullable();
            $table->string("area_solicitud")->nullable();
            $table->string("numero_hr")->nullable();
            $table->string("numero_componente")->nullable();
            $table->integer("cantidad")->nullable();
            $table->string("archivo_2d")->nullable();
            $table->string("archivo_3d")->nullable();
            $table->string("dibujo")->nullable();
            $table->boolean("tratamiento_termico")->nullable();
            $table->text("comentarios")->nullable();
            $table->string("desde")->nullable();
            $table->integer("material_id")->nullable();
            $table->integer("componente_id")->nullable();
            $table->integer("solicitante_id")->nullable();
            $table->integer("usuario_id")->nullable();
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
        Schema::dropIfExists('solicitudes_externas');
    }
}
