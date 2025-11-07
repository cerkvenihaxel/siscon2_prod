<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpTransaccionesSoapTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para logging completo de transacciones SOAP
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_transacciones_soap', function (Blueprint $table) {
            $table->id();

            // Relación opcional con solicitud
            $table->unsignedBigInteger('solicitud_id')->nullable();

            // Tipo de operación
            $table->enum('transaction_type', ['ELG', 'AP', 'ATR', 'TestWs'])->comment('Tipo de transacción SOAP');

            // Identificadores
            $table->string('msgid', 100)->nullable()->comment('ID interno del mensaje');
            $table->string('idtran', 50)->nullable()->comment('ID de transacción retornado por UP');
            $table->string('idaut', 50)->nullable()->comment('ID de autorización (solo AP)');

            // Datos del afiliado
            $table->string('afiliado_codigo', 20)->nullable();

            // Request y Response completos
            $table->longText('request_xml')->nullable()->comment('XML del request enviado');
            $table->longText('response_xml')->nullable()->comment('XML del response recibido');

            // Resultado parseado
            $table->string('status', 20)->nullable()->comment('OK/NO/PEND');
            $table->string('response_code', 50)->nullable()->comment('MSGXML_xxxx');
            $table->text('response_message')->nullable();
            $table->json('response_data')->nullable()->comment('Datos parseados del response');

            // Metadatos de ejecución
            $table->integer('execution_time_ms')->nullable()->comment('Tiempo de respuesta en milisegundos');
            $table->string('soap_endpoint', 255)->nullable()->comment('Endpoint usado');
            $table->ipAddress('client_ip')->nullable();

            // Errores
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Foreign key
            $table->foreign('solicitud_id')
                  ->references('id')
                  ->on('up_solicitudes')
                  ->onDelete('set null');

            // Índices
            $table->index('transaction_type', 'idx_transaction_type');
            $table->index('afiliado_codigo', 'idx_afiliado_codigo');
            $table->index('idtran', 'idx_idtran');
            $table->index('msgid', 'idx_msgid');
            $table->index('status', 'idx_status');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_transacciones_soap');
    }
}
