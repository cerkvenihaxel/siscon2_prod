<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnulaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anulaciones', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
	    $table->integer('afiliados_id')->nullable();
            $table->integer('edad')->nullable();
            $table->string('nrosolicitud')->nullable();
            $table->integer('clinicas_id')->nullable();
            $table->integer('estado_solicitud_id')->nullable();
            $table->integer('estado_paciente_id')->nullable();
            $table->date('fecha_cirugia')->nullable();
            $table->integer('medicos_id')->nullable();
            $table->string('autorizado')->nullable();
            $table->string('archivo')->nullable();
            $table->string('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anulaciones');
    }
}
