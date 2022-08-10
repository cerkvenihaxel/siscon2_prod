<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionesDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
	  $table->softDeletes();
            $table->integer('entrantes_id')->nullable();
            $table->integer('articulos_id')->nullable();
            $table->integer('garantia')->nullable();
            $table->integer('cantidad')->nullable();
            $table->double('precio')->nullable();       
 });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciones_detail');
    }
}
