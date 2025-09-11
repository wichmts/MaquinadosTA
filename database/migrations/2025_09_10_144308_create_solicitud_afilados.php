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
            $table->string("fecha_deseada_entrega")->nullable();
            $table->string("fecha_real_entrega")->nullable();           
            $table->string("area_solicitud")->nullable();
            $table->string("numero_hr")->nullable();
            $table->string("nombre_componente")->nullable();
            $table->integer("cantidad")->nullable();
            $table->string("archivo_2d")->nullable();            
            $table->text("comentarios")->nullable();   
            $table->string("caras_a_afilar")->nullable();
            $table->string("cuanto_afilar")->nullable();                                    
            
            $table->unsignedBigInteger('solicitante_id')-> nullable();
            $table->unsignedBigInteger('componente_id')->nullable();
            $table->timestamps();

            $table->foreign('solicitante_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('componente_id')
                ->references('id')->on('componentes')
                ->onDelete('cascade');
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
