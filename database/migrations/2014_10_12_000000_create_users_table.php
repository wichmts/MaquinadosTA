<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('cliente_id')->nullable();
            
            // general data
            $table->string('nombre');
            $table->string('ap_paterno');
            $table->string('ap_materno');
            $table->string('email')->unique();
            $table->string('celular')->nullable();
            $table->string('codigo_acceso')->nullable();            
            // configuration and useful data
            $table->boolean('active')->nullable();          
            $table->String('token', 1000)->nullable();      // for jwt
            $table->string('api_token')->nullable();        //for laravel api            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
