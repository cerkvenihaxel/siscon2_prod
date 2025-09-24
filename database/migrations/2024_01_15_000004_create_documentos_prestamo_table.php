<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_prestamo', function (Blueprint $table) {
            $table->id();
            $table->integer('prestamo_oxigenoterapia_id');
            $table->string('tipo_documento'); // TERMINOS_CONDICIONES, CONTRATO, RENOVACION, FINALIZACION
            $table->string('nombre_documento');
            $table->text('contenido_documento');
            $table->string('archivo_generado')->nullable();
            $table->date('fecha_generacion');
            $table->date('fecha_firma')->nullable();
            $table->string('firma_digital')->nullable();
            $table->string('ip_firma')->nullable();
            $table->string('estado_documento')->default('GENERADO'); // GENERADO, FIRMADO, VENCIDO
            $table->string('stamp_user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_prestamo');
    }
}; 