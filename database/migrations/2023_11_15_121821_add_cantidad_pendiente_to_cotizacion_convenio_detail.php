<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCantidadPendienteToCotizacionConvenioDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizacion_convenio_detail', function (Blueprint $table) {
            $table->integer('cantidad_pendiente')->after('cantidad_entregada')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizacion_convenio_detail', function (Blueprint $table) {
            //
        });
    }
}
