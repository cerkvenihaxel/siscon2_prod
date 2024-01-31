<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PedidoMedicamentoDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_medicamento_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('pedido_medicamento_id')->nullable();
            $table->integer('articuloZafiro_id')->nullable();
            $table->string('laboratorio')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('presentacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
