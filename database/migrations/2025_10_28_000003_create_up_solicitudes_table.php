<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     * Tabla para almacenar las solicitudes nuevas de autorización
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_solicitudes', function (Blueprint $table) {
            $table->id();

            // Identificación de la solicitud
            $table->string('nro_solicitud', 50)->unique()->comment('Número único de solicitud');
            $table->string('codigo_afiliado', 20)->comment('FK a afiliados_convenio_up');

            // Datos del prestador
            $table->string('prestador_id', 20)->nullable();
            $table->string('prestador_nombre', 200)->nullable();

            // Credenciales SOAP
            $table->string('usrid', 50)->nullable();
            $table->string('usrpass', 100)->nullable();

            // Control de flujo
            $table->enum('estado', [
                'borrador',
                'elegibilidad_ok',
                'elegibilidad_error',
                'aprobada',
                'rechazada',
                'anulada',
                'pendiente'
            ])->default('borrador');

            // Identificadores SOAP - Elegibilidad
            $table->string('msgid_elegibilidad', 100)->nullable();
            $table->string('idtran_elegibilidad', 50)->nullable();

            // Identificadores SOAP - Aprobación
            $table->string('msgid_aprobacion', 100)->nullable();
            $table->string('idtran_aprobacion', 50)->nullable();
            $table->string('idaut', 50)->nullable()->comment('ID de autorización');

            // Identificadores SOAP - Anulación
            $table->string('msgid_anulacion', 100)->nullable();
            $table->string('idtran_anulacion', 50)->nullable();

            // Metadatos
            $table->dateTime('fecha_solicitud')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('usuario_creador', 100)->nullable();

            $table->timestamps();

            // Índices
            $table->index('nro_solicitud', 'idx_nro_solicitud');
            $table->index('codigo_afiliado', 'idx_codigo_afiliado');
            $table->index('estado', 'idx_estado');
            $table->index('fecha_solicitud', 'idx_fecha_solicitud');
            $table->index('idtran_aprobacion', 'idx_idtran_aprobacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_solicitudes');
    }
}
