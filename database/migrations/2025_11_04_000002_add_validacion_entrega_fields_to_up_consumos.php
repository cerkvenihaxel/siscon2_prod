<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('up_consumos', function (Blueprint $table) {
            // Relación con validación de entrega
            $table->unsignedBigInteger('validacion_entrega_id')->nullable()->after('anulacion_id');
            $table->foreign('validacion_entrega_id')->references('id')->on('up_validaciones_entrega')->onDelete('set null');
            
            // Campos de control de validación de entrega
            $table->datetime('fecha_validacion_entrega')->nullable()->after('fecha_anulacion');
            $table->string('usuario_validacion_entrega', 100)->nullable()->after('usuario_anulacion');
            
            // Campos adicionales para el flujo mejorado
            $table->string('farmacia_codigo', 20)->nullable()->after('usuario_validacion_entrega');
            $table->string('farmacia_nombre', 255)->nullable()->after('farmacia_codigo');
            $table->boolean('requiere_receta')->default(false)->after('farmacia_nombre');
            $table->string('numero_receta', 50)->nullable()->after('requiere_receta');
            $table->text('notas_farmacia')->nullable()->after('numero_receta');
            
            // Control de stock y disponibilidad
            $table->boolean('stock_disponible')->nullable()->after('notas_farmacia');
            $table->datetime('fecha_verificacion_stock')->nullable()->after('stock_disponible');
            $table->string('usuario_verificacion_stock', 100)->nullable()->after('fecha_verificacion_stock');
            
            // Índices para optimizar consultas
            $table->index('validacion_entrega_id');
            $table->index('fecha_validacion_entrega');
            $table->index('farmacia_codigo');
            $table->index(['afiliado', 'fecha_validacion_entrega']);
            $table->index(['estado_flujo', 'fecha_validacion_entrega']);
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
            // Eliminar índices
            $table->dropIndex(['estado_flujo', 'fecha_validacion_entrega']);
            $table->dropIndex(['afiliado', 'fecha_validacion_entrega']);
            $table->dropIndex(['farmacia_codigo']);
            $table->dropIndex(['fecha_validacion_entrega']);
            $table->dropIndex(['validacion_entrega_id']);
            
            // Eliminar foreign key
            $table->dropForeign(['validacion_entrega_id']);
            
            // Eliminar columnas
            $table->dropColumn([
                'validacion_entrega_id',
                'fecha_validacion_entrega',
                'usuario_validacion_entrega',
                'farmacia_codigo',
                'farmacia_nombre',
                'requiere_receta',
                'numero_receta',
                'notas_farmacia',
                'stock_disponible',
                'fecha_verificacion_stock',
                'usuario_verificacion_stock',
            ]);
        });
    }
};
