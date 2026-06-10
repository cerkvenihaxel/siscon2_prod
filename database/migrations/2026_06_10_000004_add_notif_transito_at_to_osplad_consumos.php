<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca de tiempo del envío de la notificación "procesando" (mensaje #1).
 *
 * Permite que el comando osplad:notificar-transito sea idempotente: solo notifica
 * a los consumos que entraron a En tránsito y aún no fueron avisados.
 */
return new class extends Migration
{
    protected $connection = 'drogueria';

    public function up(): void
    {
        Schema::connection('drogueria')->table('osplad_consumos', function (Blueprint $table) {
            if (!Schema::connection('drogueria')->hasColumn('osplad_consumos', 'notif_transito_at')) {
                $table->dateTime('notif_transito_at')->nullable()->after('fecha_validacion');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('drogueria')->table('osplad_consumos', function (Blueprint $table) {
            if (Schema::connection('drogueria')->hasColumn('osplad_consumos', 'notif_transito_at')) {
                $table->dropColumn('notif_transito_at');
            }
        });
    }
};
