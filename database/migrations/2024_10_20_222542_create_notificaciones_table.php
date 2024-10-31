<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->text('roles')->nullable();
            $table->string('url_base')->nullable();
            $table->integer('anio_id')->nullable();
            $table->integer('cliente_id')->nullable();
            $table->integer('proyecto_id')->nullable();
            $table->integer('herramental_id')->nullable();
            $table->integer('componente_id')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('descripcion')->nullable();
            $table->text('responsables')->nullable();
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
        Schema::dropIfExists('notificaciones');
    }
}
