<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowFieldsToUpConsumos extends Migration
{
    /**
     * Run the migrations.
     * Agregar campos para el flujo ELG -> AP -> Entrega
     *
     * @return void
     */
    public function up()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            // Estado del flujo de trabajo
            $table->enum('estado_flujo', [
                'pendiente',        // Inicial - sin procesar
                'elegibilidad_ok',  // ELG verificado exitosamente
                'elegibilidad_no',  // ELG rechazado
                'aprobado',         // AP aprobado
                'rechazado',        // AP rechazado
                'entregado',        // Validado como entregado
                'anulado'           // ATR ejecutado
            ])->default('pendiente')->after('status');

            // Referencias a transacciones SOAP
            $table->unsignedBigInteger('elegibilidad_id')->nullable()->after('estado_flujo')->comment('FK a up_elegibilidad');
            $table->unsignedBigInteger('autorizacion_id')->nullable()->after('elegibilidad_id')->comment('FK a up_autorizacion_previa');
            $table->unsignedBigInteger('anulacion_id')->nullable()->after('autorizacion_id')->comment('FK a up_anulaciones');

            // IDs de transacciones SOAP
            $table->string('idtran_elegibilidad', 50)->nullable()->after('anulacion_id');
            $table->string('idtran_aprobacion', 50)->nullable()->after('idtran_elegibilidad');
            $table->string('idaut', 50)->nullable()->after('idtran_aprobacion')->comment('ID de autorización');

            // Control de entrega
            $table->dateTime('fecha_elegibilidad')->nullable()->after('idaut');
            $table->dateTime('fecha_aprobacion')->nullable()->after('fecha_elegibilidad');
            $table->dateTime('fecha_entrega')->nullable()->after('fecha_aprobacion');
            $table->dateTime('fecha_anulacion')->nullable()->after('fecha_entrega');

            // Usuario que realizó cada acción
            $table->string('usuario_elegibilidad', 100)->nullable()->after('fecha_anulacion');
            $table->string('usuario_aprobacion', 100)->nullable()->after('usuario_elegibilidad');
            $table->string('usuario_entrega', 100)->nullable()->after('usuario_aprobacion');
            $table->string('usuario_anulacion', 100)->nullable()->after('usuario_entrega');

            // Observaciones
            $table->text('observaciones_flujo')->nullable()->after('usuario_anulacion');

            // Índices
            $table->index('estado_flujo', 'idx_estado_flujo');
            $table->index('elegibilidad_id', 'idx_elegibilidad_id');
            $table->index('autorizacion_id', 'idx_autorizacion_id');
            $table->index('idaut', 'idx_consumo_idaut');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            $table->dropIndex('idx_estado_flujo');
            $table->dropIndex('idx_elegibilidad_id');
            $table->dropIndex('idx_autorizacion_id');
            $table->dropIndex('idx_consumo_idaut');

            $table->dropColumn([
                'estado_flujo',
                'elegibilidad_id',
                'autorizacion_id',
                'anulacion_id',
                'idtran_elegibilidad',
                'idtran_aprobacion',
                'idaut',
                'fecha_elegibilidad',
                'fecha_aprobacion',
                'fecha_entrega',
                'fecha_anulacion',
                'usuario_elegibilidad',
                'usuario_aprobacion',
                'usuario_entrega',
                'usuario_anulacion',
                'observaciones_flujo',
            ]);
        });
    }
}
