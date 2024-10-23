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
            $table->string('modelo')->nullable();           //embarque, cotizacion, cliente, etc...
            $table->integer('id_modelo')->nullable();       //Identificador del modelo asociado (Cliente, Ubicación, Transportista, 'Certificacion', 'Embarque).
            $table->string('nombre')->nullable();
            $table->string('tamano')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->string('categoria')->nullable(); // layout-cliente, msds, orden-servicio, carta-porte-incompleta, confirmacion-servicio, carta-porte, factura-proveedor, factura-cliente, otros
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
