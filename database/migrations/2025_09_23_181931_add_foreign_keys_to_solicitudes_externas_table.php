<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSolicitudesExternasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes_externas', function (Blueprint $table) {
            $table->unsignedBigInteger('material_id')->nullable()->change();
            $table->unsignedBigInteger('componente_id')->nullable()->change();
            $table->unsignedBigInteger('solicitante_id')->nullable()->change();

            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
            $table->foreign('solicitante_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('solicitudes_externas', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropForeign(['componente_id']);
            $table->dropForeign(['solicitante_id']);

            $table->integer('material_id')->nullable()->change();
            $table->integer('componente_id')->nullable()->change();
            $table->integer('solicitante_id')->nullable()->change();
        });
    }
}
