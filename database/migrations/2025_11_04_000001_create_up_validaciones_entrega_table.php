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
        Schema::create('up_validaciones_entrega', function (Blueprint $table) {
            $table->id();
            
            // Relación con consumo
            $table->unsignedBigInteger('consumo_id');
            $table->foreign('consumo_id')->references('id')->on('up_consumos')->onDelete('cascade');
            
            // Datos del afiliado
            $table->string('afiliado_codigo', 20);
            $table->string('afiliado_nombre', 255)->nullable();
            $table->string('afiliado_apellido', 255)->nullable();
            
            // Datos del medicamento/prestación
            $table->string('medicamento_codigo', 50);
            $table->text('medicamento_descripcion');
            $table->integer('cantidad');
            $table->decimal('importe_autorizado', 10, 2)->nullable();
            $table->string('idaut', 50)->nullable(); // ID de autorización de UP
            
            // Datos de la entrega
            $table->datetime('fecha_entrega');
            $table->string('usuario_entrega', 100);
            $table->string('farmacia_codigo', 20)->nullable();
            $table->string('farmacia_nombre', 255)->nullable();
            $table->string('farmacia_direccion', 500)->nullable();
            
            // Datos adicionales
            $table->text('observaciones')->nullable();
            $table->string('numero_receta', 50)->nullable();
            $table->string('medico_prescriptor', 255)->nullable();
            $table->boolean('entrega_completa')->default(true);
            $table->integer('cantidad_entregada')->nullable();
            
            // Control de calidad
            $table->string('lote_medicamento', 50)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('laboratorio', 255)->nullable();
            
            // Auditoría
            $table->string('ip_entrega', 45)->nullable();
            $table->string('dispositivo_entrega', 255)->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->string('usuario_confirmacion', 100)->nullable();
            
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('afiliado_codigo');
            $table->index('fecha_entrega');
            $table->index('medicamento_codigo');
            $table->index('farmacia_codigo');
            $table->index(['afiliado_codigo', 'fecha_entrega']);
            $table->index(['farmacia_codigo', 'fecha_entrega']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_validaciones_entrega');
    }
};
