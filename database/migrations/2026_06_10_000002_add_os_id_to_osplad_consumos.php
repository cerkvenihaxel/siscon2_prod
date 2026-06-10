<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Agrega os_id (obra social) a drogueria.osplad_consumos para hacer el flujo
 * escalable multi-obra-social, y normaliza el estado del flujo.
 *
 * - os_id        : FK lógica a obras_sociales.id (backfill = OSPLAD).
 * - estado_pedido: varchar(6) ya existente. Backfill NULL -> 'PEND'.
 *                  Códigos del flujo: PEND, TRANS, ENT, ANUL.
 */
return new class extends Migration
{
    protected $connection = 'drogueria';

    public function up(): void
    {
        Schema::connection('drogueria')->table('osplad_consumos', function (Blueprint $table) {
            if (!Schema::connection('drogueria')->hasColumn('osplad_consumos', 'os_id')) {
                $table->unsignedInteger('os_id')->nullable()->after('id_convenio')->index();
            }
        });

        // Backfill: todas las filas actuales son OSPLAD
        $ospladId = DB::connection('drogueria')->table('obras_sociales')
            ->where('codigo', 'OSPLAD')->value('id');

        if ($ospladId) {
            DB::connection('drogueria')->table('osplad_consumos')
                ->whereNull('os_id')->update(['os_id' => $ospladId]);
        }

        // Normalizar estado del flujo: filas sin estado -> PEND (pendiente)
        DB::connection('drogueria')->table('osplad_consumos')
            ->whereNull('estado_pedido')
            ->orWhere('estado_pedido', '')
            ->update(['estado_pedido' => 'PEND']);
    }

    public function down(): void
    {
        Schema::connection('drogueria')->table('osplad_consumos', function (Blueprint $table) {
            if (Schema::connection('drogueria')->hasColumn('osplad_consumos', 'os_id')) {
                $table->dropColumn('os_id');
            }
        });
    }
};
