<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PedidoMasivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_masivo', function (Blueprint $table) {
            $table->id();
            $table->integer('punto_retiro_id')->nullable();
            $table->string('tel_medico')->nullable();
            $table->string('stamp_user')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('archivo')->nullable();
            $table->string('archivo2')->nullable();
            $table->string('archivo3')->nullable();
            $table->string('archivo4')->nullable();


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
