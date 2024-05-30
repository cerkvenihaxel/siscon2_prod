<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosO2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos_o2', function (Blueprint $table) {

            $table->id();
            $table->timestamps();
            $table->integer('afiliados_id');
            $table->integer('medicos_id');
            $table->string('nro_solicitud');
            $table->string('direccion');
            $table->integer('cms_users_id');
            $table->integer('punto_retiro_id');
            $table->string('observaciones');
            $table->string('archivo1')->nullable();
            $table->string('archivo2')->nullable();
            $table->string('archivo3')->nullable();
            $table->string('archivo4')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos_o2');
    }
}
