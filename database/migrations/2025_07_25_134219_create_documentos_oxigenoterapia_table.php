<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos_oxigenoterapia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_oxigenoterapia_id');
            $table->unsignedBigInteger('prestamo_oxigenoterapia_id')->nullable();
            $table->string('tipo_documento'); // consentimiento, terminos, contrato, instrucciones, checklist, resumen
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->string('mime_type')->default('application/pdf');
            $table->integer('tamaño_bytes')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('firmado')->default(false);
            $table->timestamp('fecha_firma')->nullable();
            $table->string('firmado_por')->nullable(); // nombre de quien firmó
            $table->string('stamp_user')->nullable();
            $table->timestamps();

            $table->foreign('pedido_oxigenoterapia_id')->references('id')->on('pedido_oxigenoterapia')->onDelete('cascade');
            $table->foreign('prestamo_oxigenoterapia_id')->references('id')->on('prestamo_oxigenoterapia')->onDelete('cascade');
            
            $table->index(['pedido_oxigenoterapia_id', 'tipo_documento'], 'idx_doc_oxi_pedido_tipo');
            $table->index(['prestamo_oxigenoterapia_id', 'tipo_documento'], 'idx_doc_oxi_prestamo_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_oxigenoterapia');
    }
};
