<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaComprobanteToCotizacionConvenioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizacion_convenio', function (Blueprint $table) {
            $table->date('fecha_comprobante')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizacion_convenio', function (Blueprint $table) {
            $table->dropColumn('fecha_comprobante');
        });
    }
}
