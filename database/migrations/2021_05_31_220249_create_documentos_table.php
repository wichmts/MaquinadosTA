<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('modelo')->nullable();           //  Programacion
            $table->integer('id_modelo')->nullable();       //  ID componente
            $table->string('nombre')->nullable();
            $table->string('tamano')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->integer('componente_id')->nullable();
            $table->string('categoria')->nullable();            
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
        Schema::dropIfExists('documentos');
    }
}
