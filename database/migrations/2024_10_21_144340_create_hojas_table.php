<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHojasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hojas', function (Blueprint $table) {
            $table->id();
            $table->boolean('estatus')->nullable();
            $table->integer('consecutivo')->nullable();
            $table->string('calidad')->nullable();
            
            $table->string('espesor')->nullable();

            $table->string('largo_entrada')->nullable();
            $table->string('ancho_entrada')->nullable();
            $table->string('longitud_entrada')->nullable();
            $table->string('diametro_entrada')->nullable();
            $table->decimal('peso_entrada', 12, 2)->nullable();
            


            $table->string('largo_saldo')->nullable();
            $table->string('ancho_saldo')->nullable();
            $table->string('longitud_saldo')->nullable();
            $table->string('diametro_saldo')->nullable();
            $table->decimal('peso_saldo', 12, 2)->nullable();
            
            $table->float('precio_kilo')->nullable();
            $table->string('fecha_entrada')->nullable();
            $table->string('fecha_salida')->nullable();
            $table->string('factura')->nullable();
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
        Schema::dropIfExists('hojas');
    }
}
