<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpAutorizacionPreviaTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para almacenar autorizaciones previas (AP)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_autorizacion_previa', function (Blueprint $table) {
            $table->id();

            // Datos del afiliado
            $table->string('codigo_afiliado', 20)->comment('Código de afiliado UP');
            $table->string('plan', 20)->nullable()->comment('Plan del afiliado');
            $table->string('vercred', 20)->nullable()->comment('Versión de credencial');
            $table->string('token', 4)->nullable()->comment('TOKEN de credencial digital');
            $table->string('verifid', 50)->nullable()->default('MANUAL');

            // Datos del prestador
            $table->string('prestador_id', 20)->nullable();
            $table->string('prestador_nombre', 200)->nullable();
            $table->string('usrid', 50)->nullable();
            $table->string('usrpass', 100)->nullable();

            // Contexto de autorización
            $table->string('contexto_tipo', 10)->nullable()->default('A')->comment('A=Ambulatorio, I=Internado, etc');
            $table->date('fecha_autorizacion')->nullable();

            // Identificadores de transacción SOAP
            $table->string('msgid', 100)->nullable()->unique();
            $table->string('idtran', 50)->nullable()->comment('ID de transacción');
            $table->string('idaut', 50)->nullable()->comment('ID de autorización generado');

            // Resultado de la consulta
            $table->enum('status', ['OK', 'NO', 'PEND', 'ERROR'])->nullable();
            $table->string('response_code', 50)->nullable();
            $table->text('response_message')->nullable();

            // Datos del afiliado retornados por SOAP
            $table->string('afi_codigo', 20)->nullable();
            $table->string('afi_apellido', 100)->nullable();
            $table->string('afi_nombre', 100)->nullable();
            $table->string('afi_plan', 20)->nullable();
            $table->string('afi_plan_nombre', 200)->nullable();

            // Prestaciones autorizadas (JSON)
            $table->json('prestaciones')->nullable()->comment('Prestaciones solicitadas');
            $table->json('prestaciones_respuesta')->nullable()->comment('Respuesta detallada por prestación');

            // Importes
            $table->decimal('importe_total', 12, 2)->nullable()->default(0);
            $table->decimal('importe_os', 12, 2)->nullable()->default(0)->comment('Importe a cargo de la OS');
            $table->decimal('importe_afiliado', 12, 2)->nullable()->default(0)->comment('Cargo al afiliado');

            // Respuesta completa
            $table->json('response_data')->nullable()->comment('Respuesta completa del SOAP');

            // Metadatos
            $table->integer('execution_time_ms')->nullable();
            $table->string('usuario_creador', 100)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices
            $table->index('codigo_afiliado', 'idx_ap_codigo_afiliado');
            $table->index('status', 'idx_ap_status');
            $table->index('msgid', 'idx_ap_msgid');
            $table->index('idtran', 'idx_ap_idtran');
            $table->index('idaut', 'idx_ap_idaut');
            $table->index('created_at', 'idx_ap_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_autorizacion_previa');
    }
}
