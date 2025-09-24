<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoOxigenoterapiaMaterialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_oxigenoterapia_materiales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_oxigenoterapia_id');
            $table->unsignedBigInteger('equipos_oxigenoterapia_id');
            $table->integer('cantidad');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('pedido_oxigenoterapia_id', 'ped_mat_pedido_fk')->references('id')->on('pedido_oxigenoterapia')->onDelete('cascade');
            $table->foreign('equipos_oxigenoterapia_id', 'ped_mat_equipo_fk')->references('id')->on('equipos_oxigenoterapia')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_oxigenoterapia_materiales');
    }
} 