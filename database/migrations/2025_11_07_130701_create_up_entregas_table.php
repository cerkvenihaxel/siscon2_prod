<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpEntregasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_entregas', function (Blueprint $table) {
            $table->id();

            // Relación con consumo
            $table->unsignedBigInteger('consumo_id');
            $table->foreign('consumo_id')->references('id')->on('up_consumos')->onDelete('cascade');

            // Datos del afiliado (denormalizados para consulta rápida)
            $table->string('codigo_afiliado', 50);
            $table->string('nombre_afiliado', 200);

            // Datos de la prestación
            $table->string('cod_prestacion', 50);
            $table->text('descripcion_prestacion');
            $table->integer('cantidad_solicitada');
            $table->integer('cantidad_entregada');

            // Datos de la entrega
            $table->date('fecha_entrega');
            $table->enum('estado_entrega', ['completa', 'parcial', 'rechazada'])->default('completa');
            $table->text('observaciones')->nullable();

            // Usuario que registra
            $table->string('usuario_entrega', 100);

            // Archivos adjuntos
            $table->string('archivo_consentimiento')->nullable();
            $table->json('archivos_adjuntos')->nullable();

            // Firma digital o comprobante
            $table->string('firma_afiliado')->nullable();
            $table->string('dni_afiliado')->nullable();

            // Datos de logística
            $table->string('remito', 100)->nullable();
            $table->string('transporte', 100)->nullable();
            $table->string('quien_recibe', 200)->nullable();
            $table->string('relacion_afiliado', 100)->nullable();

            $table->timestamps();

            // Índices
            $table->index('consumo_id');
            $table->index('codigo_afiliado');
            $table->index('fecha_entrega');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_entregas');
    }
}
