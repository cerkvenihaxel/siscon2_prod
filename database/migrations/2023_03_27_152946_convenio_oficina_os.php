<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('convenio_oficina_os', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('afiliados_id')->nullable();
            $table->integer('nroAfiliado')->nullable();
            $table->string('obra_social')->nullable();
            $table->integer('edad')->nullable();
            $table->string('nrosolicitud')->nullable();
            $table->integer('clinicas_id')->nullable();
            $table->integer('medicos_id')->nullable();
            $table->string('zona_residencia')->nullable();
            $table->string('tel_afiliado')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_receta')->nullable();
            $table->string('postdatada')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('estado_solicitud_id')->nullable();
            $table->string('tel_medico')->nullable();
            $table->string('stamp_user')->nullable();
            $table->string('proveedor')->nullable();
            $table->string('patologia')->nullable();
            $table->string('discapacidad')->nullable();
            $table->string('observaciones')->nullable();
            $table->integer('estado_pedido_id')->nullable();
            $table->string('archivo')->nullable();
            $table->string('archivo2')->nullable();
            $table->string('archivo3')->nullable();
            $table->string('archivo4')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('convenio_oficina_os', function (Blueprint $table) {
            //
        });
    }
};
