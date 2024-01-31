<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PresentacionDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presentacion_detail', function (Blueprint $table){
           $table->id();
           $table->timestamps();
           $table->integer('presentacion_id');
           $table->integer('articulos_id');
           $table->integer('garantia_id');
           $table->integer('cantidad');
           $table->float('precio_unitario');
           $table->integer('procedencias_id');
           $table->string('stickers');
           $table->string('foja')->nullable();
           $table->float('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presentacion_detail');
    }
}
