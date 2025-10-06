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

            $table->unsignedBigInteger('herramental_id')->nullable();
            $table->foreign('herramental_id')->references('id')->on('herramentales')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

       

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentacion_tecnica', function (Blueprint $table) {
        $table->dropForeign(['herramental_id']);
        $table->dropForeign(['user_id']);
    });
        Schema::dropIfExists('documentacion_tecnica');
    }
}
