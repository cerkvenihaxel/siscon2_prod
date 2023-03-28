<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotizacion_convenio', function (Blueprint $table) {
            $table->id();

            $table->integer('numeroID')->nullable();
            $table->string('fecha_carga')->nullable();
            $table->string('nombreyapellido')->nullable();
            $table->string('documento')->nullable();
            $table->string('nroAfiliado')->nullable();
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
            $table->date('fecha_entrega')->nullable();
            $table->integer('estado_solicitud_id')->nullable();
            $table->integer('estado_pedido_id')->nullable();
            $table->integer('punto_retiro_id')->nullable();
            $table->string('tel_medico')->nullable();
            $table->string('proveedor')->nullable();
            $table->string('stamp_user')->nullable();
            $table->string('discapacidad')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('archivo')->nullable();
            $table->string('archivo2')->nullable();
            $table->string('archivo3')->nullable();
            $table->string('archivo4')->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_convenio');
    }
};
