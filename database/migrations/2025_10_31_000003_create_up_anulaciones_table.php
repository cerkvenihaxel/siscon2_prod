<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpAnulacionesTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para almacenar anulaciones de transacciones (ATR)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_anulaciones', function (Blueprint $table) {
            $table->id();

            // Datos del afiliado
            $table->string('codigo_afiliado', 20)->comment('Código de afiliado UP');
            $table->string('plan', 20)->nullable()->comment('Plan del afiliado');
            $table->string('vercred', 20)->nullable()->comment('Versión de credencial');
            $table->string('token', 4)->nullable()->comment('TOKEN de credencial digital');

            // Credenciales SOAP
            $table->string('usrid', 50)->nullable();
            $table->string('usrpass', 100)->nullable();

            // Datos de la anulación
            $table->enum('tipoidanul', ['IDTRAN', 'MSGID', 'IDAUT'])->comment('Tipo de ID para anular');
            $table->string('idanul', 100)->comment('ID de la transacción a anular');
            $table->text('motivo')->nullable()->comment('Motivo de la anulación');
            $table->date('fecha_anulacion')->nullable();

            // Identificadores de transacción SOAP
            $table->string('msgid', 100)->nullable()->unique()->comment('MSGID de esta anulación');
            $table->string('idtran', 50)->nullable()->comment('IDTRAN de esta anulación');

            // Resultado de la consulta
            $table->enum('status', ['OK', 'NO', 'PEND', 'ERROR'])->nullable();
            $table->string('response_code', 50)->nullable();
            $table->text('response_message')->nullable();

            // Datos del afiliado retornados por SOAP
            $table->string('afi_codigo', 20)->nullable();
            $table->string('afi_apellido', 100)->nullable();
            $table->string('afi_nombre', 100)->nullable();

            // ID de autorización anulado (si corresponde)
            $table->string('idaut_anulado', 50)->nullable()->comment('IDAUT de la transacción anulada');

            // Respuesta completa
            $table->json('response_data')->nullable()->comment('Respuesta completa del SOAP');

            // Metadatos
            $table->integer('execution_time_ms')->nullable();
            $table->string('usuario_creador', 100)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices
            $table->index('codigo_afiliado', 'idx_atr_codigo_afiliado');
            $table->index('status', 'idx_atr_status');
            $table->index('msgid', 'idx_atr_msgid');
            $table->index('idtran', 'idx_atr_idtran');
            $table->index('tipoidanul', 'idx_atr_tipoidanul');
            $table->index('idanul', 'idx_atr_idanul');
            $table->index('created_at', 'idx_atr_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_anulaciones');
    }
}
