<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ValidacionesFarmacias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validaciones_farmacias', function (Blueprint $table){
            $table->id();
            $table->timestamps();
            $table->integer('cotizacion_convenio_id');
            $table->integer('farmacia_id');
            $table->integer('estado_solicitud_id');
            $table->integer('estado_pedido_id');
            $table->string('nro_solicitud');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
