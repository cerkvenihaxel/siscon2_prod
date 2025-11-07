<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpElegibilidadTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para almacenar consultas de elegibilidad (ELG)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_elegibilidad', function (Blueprint $table) {
            $table->id();

            // Datos del afiliado
            $table->string('codigo_afiliado', 20)->comment('Código de afiliado UP');
            $table->string('plan', 20)->nullable()->comment('Plan del afiliado');
            $table->string('vercred', 20)->nullable()->comment('Versión de credencial');
            $table->string('token', 4)->nullable()->comment('TOKEN de credencial digital');
            $table->string('verifid', 50)->nullable()->default('MANUAL');

            // Datos del prestador
            $table->string('prestador_id', 20)->nullable();
            $table->string('usrid', 50)->nullable();
            $table->string('usrpass', 100)->nullable();

            // Identificadores de transacción SOAP
            $table->string('msgid', 100)->nullable()->unique();
            $table->string('idtran', 50)->nullable();

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
            $table->string('afi_sexo', 1)->nullable();
            $table->date('afi_fecha_nacimiento')->nullable();
            $table->string('afi_codigo_postal', 10)->nullable();
            $table->string('afi_localidad', 100)->nullable();
            $table->string('afi_provincia', 100)->nullable();
            $table->string('afi_vercred', 20)->nullable();

            // Prestaciones consultadas (JSON)
            $table->json('prestaciones')->nullable()->comment('Prestaciones consultadas en el ELG');
            $table->json('response_data')->nullable()->comment('Respuesta completa del SOAP');

            // Metadatos
            $table->integer('execution_time_ms')->nullable();
            $table->string('usuario_creador', 100)->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices
            $table->index('codigo_afiliado', 'idx_elg_codigo_afiliado');
            $table->index('status', 'idx_elg_status');
            $table->index('msgid', 'idx_elg_msgid');
            $table->index('idtran', 'idx_elg_idtran');
            $table->index('created_at', 'idx_elg_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_elegibilidad');
    }
}
