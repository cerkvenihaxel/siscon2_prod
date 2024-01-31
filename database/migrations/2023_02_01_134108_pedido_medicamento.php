<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PedidoMedicamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_medicamento', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('afiliados_id')->nullable();
            $table->integer('nroAfiliado')->nullable();
            $table->integer('edad')->nullable();
            $table->string('nrosolicitud')->nullable();
            $table->integer('clinicas_id')->nullable();
            $table->integer('medicos_id')->nullable();
            $table->string('zona_residencia')->nullable();
            $table->integer('tel_afiliado')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_receta')->nullable();
            $table->string('postdatada')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('estado_solicitud_id')->nullable();
            $table->integer('tel_medico')->nullable();
            $table->string('stamp_user')->nullable();
            $table->string('discapacidad')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('archivo')->nullable();
            $table->string('archivo2')->nullable();
            $table->string('archivo3')->nullable();
            $table->string('archivo4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_medicamento');
    }
}
