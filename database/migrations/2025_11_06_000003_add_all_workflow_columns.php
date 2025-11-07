<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllWorkflowColumns extends Migration
{
    public function up()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            if (!Schema::hasColumn('up_consumos', 'remito')) {
                $table->string('remito')->nullable();
            }
            if (!Schema::hasColumn('up_consumos', 'nro_pedido')) {
                $table->string('nro_pedido')->nullable();
            }
            if (!Schema::hasColumn('up_consumos', 'nro_transporte')) {
                $table->string('nro_transporte')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            $table->dropColumn(['remito', 'nro_pedido', 'nro_transporte']);
        });
    }
}
