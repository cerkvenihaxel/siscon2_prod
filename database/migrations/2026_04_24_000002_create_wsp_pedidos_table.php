<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wsp_pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('pedido_id', 50)->unique();
            $table->string('usuario_id', 50)->nullable();
            $table->unsignedSmallInteger('sucursal_id')->nullable();
            $table->string('cliente_nombre', 200)->nullable();
            $table->string('cliente_dni', 30)->nullable();
            $table->json('medicamentos');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('tipo_envio', 30)->nullable();
            $table->text('ubicacion')->nullable();
            $table->string('metodo_pago', 30)->nullable();
            $table->enum('estado', ['CONFIRMADO','PROCESANDO','ENTREGADO','CANCELADO'])->default('CONFIRMADO');
            $table->text('notas')->nullable();
            $table->timestamp('pedido_at')->nullable();
            $table->timestamps();

            $table->index('usuario_id');
            $table->index('estado');
            $table->index('pedido_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wsp_pedidos');
    }
};
