<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoHojasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_hojas', function (Blueprint $table) {
            $table->id();
            $table->string('largo')->nullable();
            $table->string('ancho')->nullable();
            $table->string('peso')->nullable();
            $table->integer('hoja_id')->nullable();
            $table->integer('proyecto_id')->nullable();
            $table->integer('componente_id')->nullable();
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
        Schema::dropIfExists('movimiento_hojas');
    }
}
