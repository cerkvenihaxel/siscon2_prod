<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columnas de seguimiento de la confirmación por WhatsApp del afiliado.
 *
 * Flujo: en PENDIENTES se le envía al afiliado la lista de su medicación por
 * WhatsApp. El afiliado responde 1 (Confirmar retiro) o 2 (Cancelar). El webhook
 * actualiza estado_pedido a 'CONF' (Confirmado por afiliado) y fecha_validacion.
 *
 * - notif_confirmacion_at   : cuándo se le envió el pedido de confirmación (idempotencia).
 * - confirmacion_respuesta  : última respuesta cruda del afiliado ('1', '2', texto libre).
 * - confirmacion_respuesta_at: cuándo respondió.
 *
 * El estado 'CONF' reutiliza la columna existente estado_pedido (varchar 6).
 */
return new class extends Migration
{
    protected $connection = 'drogueria';

    public function up(): void
    {
        Schema::connection('drogueria')->table('osplad_consumos', function (Blueprint $table) {
            if (!Schema::connection('drogueria')->hasColumn('osplad_consumos', 'notif_confirmacion_at')) {
                $table->dateTime('notif_confirmacion_at')->nullable()->after('notif_transito_at');
            }
            if (!Schema::connection('drogueria')->hasColumn('osplad_consumos', 'confirmacion_respuesta')) {
                $table->string('confirmacion_respuesta', 50)->nullable()->after('notif_confirmacion_at');
            }
            if (!Schema::connection('drogueria')->hasColumn('osplad_consumos', 'confirmacion_respuesta_at')) {
                $table->dateTime('confirmacion_respuesta_at')->nullable()->after('confirmacion_respuesta');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('drogueria')->table('osplad_consumos', function (Blueprint $table) {
            foreach (['confirmacion_respuesta_at', 'confirmacion_respuesta', 'notif_confirmacion_at'] as $col) {
                if (Schema::connection('drogueria')->hasColumn('osplad_consumos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
