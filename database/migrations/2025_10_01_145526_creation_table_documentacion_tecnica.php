<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreationTableDocumentacionTecnica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentacion_tecnica', function (Blueprint $table) {
            $table->id();
            $table->string("archivo")->nullable();
            $table->string("descripcion")->nullable(); 
            $table->timestamps();           
        });

        Schema::table('herramentales', function (Blueprint $table) {
            $table->unsignedBigInteger('documentacion_tecnica_id')->nullable();
            $table->foreign('documentacion_tecnica_id')
                ->references('id')->on('documentacion_tecnica')
                ->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('documentacion_tecnica_id')->nullable();
            $table->foreign('documentacion_tecnica_id')
                ->references('id')->on('documentacion_tecnica')
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
        Schema::table('herramentales', function (Blueprint $table) {
            $table->dropForeign(['documentacion_tecnica_id']);
            $table->dropColumn('documentacion_tecnica_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['documentacion_tecnica_id']); 
            $table->dropColumn('documentacion_tecnica_id');
        });

        Schema::dropIfExists('documentacion_tecnica');
    }
}
