<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadDeMedida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidad_de_medida', function (Blueprint $table) {
            $table->id();
            $table->string("nombre")->nullable();
            $table->string("abreviatura")->nullable();
            $table->timestamps();
        });

        Schema::table('solicitud_afilados', function (Blueprint $table) {
        $table->unsignedBigInteger('unidad_medida_id')->nullable();

        $table->foreign('unidad_medida_id')
              ->references('id')->on('unidad_de_medida')
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

        Schema::table('solicitud_afilados', function (Blueprint $table) {
            $table->dropForeign(['unidad_medida_id']);
            $table->dropColumn('unidad_medida_id');            
        });
        Schema::dropIfExists('unidad_de_medida');
    }
}
