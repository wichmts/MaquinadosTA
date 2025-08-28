<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    public function up()
    {
        // 🔹 pruebas_diseno
        Schema::table('pruebas_diseno', function (Blueprint $table) {
            $table->unsignedBigInteger('herramental_id')->change();
            $table->unsignedBigInteger('usuario_id')->change();

            $table->foreign('herramental_id')->references('id')->on('herramentales')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 🔹 herramentales
        Schema::table('herramentales', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id')->change();

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
        });

        // 🔹 documentos
        Schema::table('documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->change();
            $table->unsignedBigInteger('componente_id')->change();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
        });

        // 🔹 componentes
        Schema::table('componentes', function (Blueprint $table) {
            $table->unsignedBigInteger('matricero_id')->change();
            $table->unsignedBigInteger('programador_id')->change();
            $table->unsignedBigInteger('herramental_id')->change();
            $table->unsignedBigInteger('material_id')->change();

            $table->foreign('matricero_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('programador_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('herramental_id')->references('id')->on('herramentales')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('cascade');
        });

        // 🔹 pruebas_proceso
        Schema::table('pruebas_proceso', function (Blueprint $table) {
            $table->unsignedBigInteger('herramental_id')->change();
            $table->unsignedBigInteger('usuario_id')->change();

            $table->foreign('herramental_id')->references('id')->on('herramentales')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 🔹 fabricaciones
        Schema::table('fabricaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('maquina_id')->change();
            $table->unsignedBigInteger('usuario_id')->change();
            $table->unsignedBigInteger('componente_id')->change();

            $table->foreign('maquina_id')->references('id')->on('maquinas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
        });

        // 🔹 notificaciones
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('anio_id')->change();
            $table->unsignedBigInteger('cliente_id')->change();
            $table->unsignedBigInteger('proyecto_id')->change();
            $table->unsignedBigInteger('herramental_id')->change();
            $table->unsignedBigInteger('fabricacion_id')->change();
            $table->unsignedBigInteger('maquina_id')->change();
            $table->unsignedBigInteger('componente_id')->change();

            $table->foreign('anio_id')->references('id')->on('anios')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('herramental_id')->references('id')->on('herramentales')->onDelete('cascade');
            $table->foreign('fabricacion_id')->references('id')->on('fabricaciones')->onDelete('cascade');
            $table->foreign('maquina_id')->references('id')->on('maquinas')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
        });

        // 🔹 seguimiento_tiempos
        Schema::table('seguimiento_tiempos', function (Blueprint $table) {        
            $table->unsignedBigInteger('componente_id')->change();
            $table->unsignedBigInteger('herramental_id')->change();
            $table->unsignedBigInteger('usuario_id')->change();
            $table->unsignedBigInteger('fabricacion_id')->change();            
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
            $table->foreign('herramental_id')->references('id')->on('herramentales')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('fabricacion_id')->references('id')->on('fabricaciones')->onDelete('cascade');
        });

        // 🔹 movimiento_hojas
        Schema::table('movimiento_hojas', function (Blueprint $table) {
            $table->unsignedBigInteger('hoja_id')->change();
            $table->unsignedBigInteger('proyecto_id')->change();
            $table->unsignedBigInteger('componente_id')->change();

            $table->foreign('hoja_id')->references('id')->on('hojas')->onDelete('cascade');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
        });

        // 🔹 solicitudes
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->change();
            $table->unsignedBigInteger('componente_id')->change();
            $table->unsignedBigInteger('fabricacion_id')->change();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade');
            $table->foreign('fabricacion_id')->references('id')->on('fabricaciones')->onDelete('cascade');
        });

        // 🔹 clientes
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('anio_id')->change();

            $table->foreign('anio_id')->references('id')->on('anios')->onDelete('cascade');
        });

        // 🔹 users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->change();
            $table->unsignedBigInteger('puesto_id')->change();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
        });

        // 🔹 hojas
        Schema::table('hojas', function (Blueprint $table) {
            $table->unsignedBigInteger('material_id')->change();

            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('cascade');
        });

        // 🔹 proyectos
        Schema::table('proyectos', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->change();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Se eliminan las foreign keys en orden inverso
        Schema::table('pruebas_diseno', function (Blueprint $table) {
            $table->dropForeign(['herramental_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::table('herramentales', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['componente_id']);
        });

        Schema::table('componentes', function (Blueprint $table) {
            $table->dropForeign(['matricero_id']);
            $table->dropForeign(['programador_id']);
            $table->dropForeign(['herramental_id']);
            $table->dropForeign(['material_id']);
        });

        Schema::table('pruebas_proceso', function (Blueprint $table) {
            $table->dropForeign(['herramental_id']);
            $table->dropForeign(['usuario_id']);
        });

        Schema::table('fabricaciones', function (Blueprint $table) {
            $table->dropForeign(['maquina_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['componente_id']);
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['anio_id']);
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['proyecto_id']);
            $table->dropForeign(['herramental_id']);
            $table->dropForeign(['fabricacion_id']);
            $table->dropForeign(['maquina_id']);
            $table->dropForeign(['componente_id']);
        });

        Schema::table('seguimiento_tiempos', function (Blueprint $table) {        
            $table->dropForeign(['componente_id']);
            $table->dropForeign(['herramental_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['fabricacion_id']);
        });

        Schema::table('movimiento_hojas', function (Blueprint $table) {
            $table->dropForeign(['hoja_id']);
            $table->dropForeign(['proyecto_id']);
            $table->dropForeign(['componente_id']);
        });

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['componente_id']);
            $table->dropForeign(['fabricacion_id']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['anio_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['puesto_id']);
        });

        Schema::table('hojas', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });

        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
    });
    }
}