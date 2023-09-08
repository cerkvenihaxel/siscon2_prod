<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProveedoresProtesis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores_protesis', function (Blueprint $table){
           $table->id();
           $table->timestamps();
           $table->string('name');
           $table->string('convenio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedores_protesis', function (Blueprint $table){
           $table->dropColumn('name');
              $table->dropColumn('convenio');
        });
    }
}
