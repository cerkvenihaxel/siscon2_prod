<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemitoToUpConsumos extends Migration
{
    public function up()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            $table->string('remito')->nullable()->after('usuario_carga');
        });
    }

    public function down()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            $table->dropColumn('remito');
        });
    }
}
