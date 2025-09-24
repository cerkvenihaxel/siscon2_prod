<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreEquipoToPedidoOxigenoterapiaMaterialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedido_oxigenoterapia_materiales', function (Blueprint $table) {
            $table->string('nombre_equipo')->nullable()->after('equipos_oxigenoterapia_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedido_oxigenoterapia_materiales', function (Blueprint $table) {
            $table->dropColumn('nombre_equipo');
        });
    }
}
