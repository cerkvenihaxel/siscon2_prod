<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_oxigenoterapia', function (Blueprint $table) {
            $table->id();
            $table->string('nro_solicitud')->unique();
            $table->integer('afiliados_id');
            $table->string('nro_afiliado');
            $table->string('nombre_apellido');
            $table->string('documento');
            $table->integer('edad');
            $table->integer('clinicas_id');
            $table->integer('medicos_id');
            $table->string('zona_residencia');
            $table->string('tel_afiliado')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_prescripcion');
            $table->date('fecha_vencimiento');
            $table->integer('estado_oxigenoterapia_id')->default(1); // 1 = PENDIENTE
            $table->string('tel_medico')->nullable();
            $table->string('stamp_user');
            $table->text('observaciones')->nullable();
            $table->string('archivo_prescripcion')->nullable();
            $table->string('archivo_estudios')->nullable();
            $table->string('archivo_otros')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_oxigenoterapia');
    }
}; 