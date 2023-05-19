<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('id_empresa');
            $table->string('id_pedido');
            $table->date('fecha_pedido');
            $table->string('estado_pedido')->nullable();
            $table->unsignedBigInteger('id_sucursal')->nullable();
            $table->unsignedBigInteger('_origen_id_sucursal');
            $table->string('drogueria')->nullable();
            $table->unsignedBigInteger('id_cliente');
            $table->string('observaciones')->nullable();
            $table->string('nrosolicitud')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
}