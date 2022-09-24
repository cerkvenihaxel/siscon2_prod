<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresentacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presentacion', function (Blueprint $table) {

$table->id();
            $table->timestamps();
            $table->string('nombreAfiliado');
            $table->string('nroAfiliado');
            $table->string('nroSolicitud');
            $table->string('materialEntregado');
            $table->string('medicoPrestador');
            $table->string('institucion');
            $table->string('nroRemito');
            $table->string('nroFactura');
            $table->string('cantidad');
            $table->string('stickers'); //si/no
            $table->string('fojaQuirurgica'); //si/no
            $table->string('fechaCirugia');
            $table->string('fechaEntrega');
            $table->string('precioTotal');
            $table->string('proveedor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presentacion');
    }
}
