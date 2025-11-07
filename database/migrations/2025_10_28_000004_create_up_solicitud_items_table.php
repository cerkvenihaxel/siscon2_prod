<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpSolicitudItemsTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para almacenar los ítems/prestaciones de cada solicitud
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_solicitud_items', function (Blueprint $table) {
            $table->id();

            // Relación con solicitud
            $table->unsignedBigInteger('solicitud_id');

            // Datos de la prestación
            $table->char('tipo_prestacion', 1)->comment('P=Prestación, M=Medicamento, D=Derivación');
            $table->string('cod_prestacion', 50);
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->default(1);

            // Específico para medicamentos (tipo M)
            $table->string('troquel', 50)->nullable();
            $table->string('cod_barra', 50)->nullable();

            // Importes (completados después del SOAP)
            $table->decimal('cargo', 12, 2)->nullable()->comment('Cargo al afiliado');
            $table->decimal('impos', 12, 2)->nullable()->comment('Importe obra social');
            $table->decimal('impot', 12, 2)->nullable()->comment('Importe otros');
            $table->decimal('imptot', 12, 2)->nullable()->comment('Importe total');
            $table->decimal('adic', 12, 2)->nullable()->comment('Adicional');

            // Estado individual del ítem
            $table->enum('estado_item', ['pendiente', 'OK', 'NO', 'PEND'])->default('pendiente');
            $table->string('rspcodp', 50)->nullable()->comment('Código de respuesta del ítem');
            $table->text('rspmsgp')->nullable()->comment('Mensaje de respuesta del ítem');

            $table->timestamps();

            // Foreign key
            $table->foreign('solicitud_id')
                  ->references('id')
                  ->on('up_solicitudes')
                  ->onDelete('cascade');

            // Índices
            $table->index('solicitud_id', 'idx_solicitud_id');
            $table->index('tipo_prestacion', 'idx_tipo_prestacion');
            $table->index('cod_prestacion', 'idx_cod_prestacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_solicitud_items');
    }
}
