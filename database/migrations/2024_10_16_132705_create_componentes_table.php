<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComponentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('componentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('archivo_2d')->nullable();
            $table->string('archivo_3d')->nullable();
            $table->string('archivo_explosionado')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('largo')->nullable();
            $table->string('ancho')->nullable();
            $table->string('alto')->nullable();
            $table->string('peso')->nullable();
            $table->string('precio_kilo')->nullable();
            $table->string('fecha_solicitud')->nullable();
            $table->string('fecha_pedido')->nullable();
            $table->string('fecha_estimada')->nullable();
            $table->string('fecha_real')->nullable();
            
            
            $table->boolean('es_compra')->nullable();

            $table->string('area')->nullable();          // diseno-creacion, compras, corte, ensamble ...
            $table->boolean('cargado')->nullable();
            $table->boolean('comprado')->nullable();
            $table->boolean('cortado')->nullable();
            $table->boolean('ensamblado')->nullable();
            $table->string('estatus_corte')->nullable(); // inicial, proceso, pausado, finalizado
            $table->boolean('cancelado')->nullable();
            
            $table->integer('herramental_id')->nullable();
            $table->integer('material_id')->nullable();

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
        Schema::dropIfExists('componentes');
    }
}
