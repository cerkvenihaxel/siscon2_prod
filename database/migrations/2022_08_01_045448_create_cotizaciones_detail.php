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
<<<<<<< HEAD:database/migrations/2022_08_01_060212_create_cotizaciones_detail.php
            $table->double('precio')->nullable();       
 });
=======
            $table->double('precio')->nullable();
    
        });
>>>>>>> 6d0e1d8c3836d65dfd799117255f7a9325487202:database/migrations/2022_08_01_045448_create_cotizaciones_detail.php
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
