<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mapea un usuario de farmacia (cms_users) a su farmacia en droguería.
 *
 * osplad_consumos.id_cliente identifica la farmacia. Guardando ese mismo valor en
 * cms_users.id_cliente, un usuario "Farmacias OSPLAD" solo verá sus propios consumos.
 * Los usuarios Super Admin / Administrador General ven todas las farmacias.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_users', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_users', 'id_cliente')) {
                $table->bigInteger('id_cliente')->nullable()->after('obra_social_id')
                      ->comment('Farmacia en droguería (osplad_consumos.id_cliente)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_users', function (Blueprint $table) {
            if (Schema::hasColumn('cms_users', 'id_cliente')) {
                $table->dropColumn('id_cliente');
            }
        });
    }
};
