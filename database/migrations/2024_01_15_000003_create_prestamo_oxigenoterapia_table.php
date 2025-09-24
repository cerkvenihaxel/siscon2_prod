<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamo_oxigenoterapia', function (Blueprint $table) {
            $table->id();
            $table->integer('pedido_oxigenoterapia_id');
            $table->string('nro_prestamo')->unique();
            $table->date('fecha_inicio_prestamo');
            $table->date('fecha_fin_prestamo');
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_devolucion')->nullable();
            $table->string('tipo_direccion'); // PARTICULAR o CLINICA
            $table->string('direccion_entrega');
            $table->string('localidad_entrega');
            $table->string('provincia_entrega');
            $table->string('codigo_postal')->nullable();
            $table->string('telefono_contacto');
            $table->string('nombre_contacto');
            $table->text('observaciones_entrega')->nullable();
            $table->string('equipo_entregado')->nullable();
            $table->string('nro_serie_equipo')->nullable();
            $table->string('estado_prestamo')->default('ACTIVO'); // ACTIVO, FINALIZADO, RENOVADO
            $table->string('stamp_user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamo_oxigenoterapia');
    }
}; 