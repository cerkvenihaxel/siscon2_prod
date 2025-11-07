<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpConsumosTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para almacenar histórico de consumos (importados del CSV)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_consumos', function (Blueprint $table) {
            $table->id();

            // Datos del afiliado
            $table->string('afiliado', 20)->nullable();
            $table->string('apellidos', 100)->nullable();
            $table->string('nombres', 100)->nullable();
            $table->string('modelo_plan', 10)->nullable();
            $table->string('nombre_modelo_plan', 100)->nullable();

            // Ubicación
            $table->string('codigopostal', 10)->nullable();
            $table->string('localidad', 100)->nullable();
            $table->string('provincia', 100)->nullable();

            // Datos personales
            $table->integer('edad')->nullable();
            $table->string('tipo_afiliado', 20)->nullable();
            $table->string('titofam', 10)->nullable();

            // Datos de la transacción
            $table->dateTime('fecha_tran')->nullable();
            $table->integer('prestacion')->nullable()->comment('Número de orden de prestación');
            $table->char('tipo_pres', 1)->nullable()->comment('M/P/D');
            $table->string('cod_prestacion', 50)->nullable();
            $table->integer('cant')->nullable();
            $table->text('desc')->nullable()->comment('Descripción de la prestación');
            $table->string('status', 20)->nullable()->comment('OK/NO/PEND');

            // Importes
            $table->decimal('cargo', 12, 2)->nullable();
            $table->decimal('impos', 12, 2)->nullable();
            $table->decimal('impot', 12, 2)->nullable();
            $table->decimal('imptot', 12, 2)->nullable();
            $table->decimal('adic', 12, 2)->nullable();

            // Datos del prestador/efector
            $table->string('nombre_efector', 200)->nullable();
            $table->string('emisor_app', 50)->nullable();
            $table->string('num_tran', 50)->nullable();
            $table->string('cod_prestador', 20)->nullable();
            $table->string('consultorio_prestador', 100)->nullable();
            $table->string('efector_nombre', 200)->nullable();
            $table->string('consultorio_nombre', 100)->nullable();

            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index('afiliado', 'idx_afiliado');
            $table->index('fecha_tran', 'idx_fecha_tran');
            $table->index('status', 'idx_status');
            $table->index('cod_prestador', 'idx_cod_prestador');
            $table->index('tipo_pres', 'idx_tipo_pres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_consumos');
    }
}
