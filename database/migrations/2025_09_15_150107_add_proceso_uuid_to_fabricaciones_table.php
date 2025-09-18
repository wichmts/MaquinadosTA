<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcesoUuidToFabricacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabricaciones', function (Blueprint $table) {
            $table->string('proceso_uuid')->nullable()->after('maquina_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('fabricaciones', function (Blueprint $table) {
            $table->dropColumn('proceso_uuid');
        });
    }
}
