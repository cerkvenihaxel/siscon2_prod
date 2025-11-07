<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowColumnsToUpConsumos extends Migration
{
    public function up()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            $table->string('nro_pedido')->nullable()->after('remito');
            $table->string('nro_transporte')->nullable()->after('nro_pedido');
        });
    }

    public function down()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            $table->dropColumn(['nro_pedido', 'nro_transporte']);
        });
    }
}
