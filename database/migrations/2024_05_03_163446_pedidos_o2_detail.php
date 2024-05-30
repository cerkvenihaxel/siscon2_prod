<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PedidosO2Detail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos_o2_detail', function (Blueprint $table) {

            $table->id();
            $table->integer('pedidos_o2_id')->nullable();
            $table->integer('articuloZafiro_id')->nullable();
            $table->string('laboratorio')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('presentacion')->nullable();
            $table->string('precio')->nullable();
            $table->string('descuento')->nullable();
            $table->string('total')->nullable();
            $table->string('observacion')->nullable();
            $table->timestamps();

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
