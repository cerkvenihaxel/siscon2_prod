<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedido_oxigenoterapia_materiales', function (Blueprint $table) {
            $table->boolean('entregable')->default(true)->after('observaciones');
            $table->text('observaciones_entrega')->nullable()->after('entregable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedido_oxigenoterapia_materiales', function (Blueprint $table) {
            $table->dropColumn(['entregable', 'observaciones_entrega']);
        });
    }
};
