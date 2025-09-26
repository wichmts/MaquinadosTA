<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComponentesCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('componentes_compra', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->text('proveedor')->nullable();
            $table->integer('cantidad')->nullable();
            // por si viene de un componente de compra (sobrantes del ensamble)
            $table->unsignedBigInteger('componente_id')->nullable();
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
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
        Schema::dropIfExists('componentes_compra');
    }
}
