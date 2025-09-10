<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudAfilados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud_afilados', function (Blueprint $table) {
            $table->id();
            $table->string("fecha_solicitud")->nullable();
            $table->string("fecha_entrega_solicitada")->nullable();            
            $table->string("area_solicitud")->nullable();
            $table->string("numero_hr")->nullable();
            $table->string("nombre_componente")->nullable();
            $table->integer("cantidad")->nullable();
            $table->string("archivo_2d")->nullable();            
            $table->text("comentarios")->nullable();   
            $table->string("caras_a_afilar")->nullable();
            $table->string("cuanto_afilar")->nullable();                         
            $table->integer("componente_id")->nullable();
            $table->integer("solicitante_id")->nullable();            
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
        Schema::dropIfExists('solicitud_afilados');
    }
}
