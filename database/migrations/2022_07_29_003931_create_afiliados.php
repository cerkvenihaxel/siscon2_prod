<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfiliados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afiliados', function (Blueprint $table) {
            $table->id();
            $table->string('nroAfiliado')->nullable();
            $table->string('documento')->nullable();
            $table->string('apeynombres')->nullable();
            $table->string('localidad')->nullable();
            $table->string('telefonos')->nullable();
            $table->string('email')->nullable();
            $table->string('sexo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afiliados');
    }
}
