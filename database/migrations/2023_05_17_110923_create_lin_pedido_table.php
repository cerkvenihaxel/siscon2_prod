<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinPedidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lin_pedido', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('id_pedido');
            $table->string('item');
            $table->string('id_articulo');
            $table->integer('cantidad');
            $table->string('des_articulo');
            $table->string('presentacion');
            $table->float('pcio_vta_unisiva');
            $table->float('pcio_iva_comsiva');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lin_pedido');
    }
}