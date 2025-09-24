<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos_oxigenoterapia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_equipo')->unique();
            $table->string('nombre_equipo');
            $table->string('marca');
            $table->string('modelo');
            $table->string('nro_serie')->unique();
            $table->string('tipo_equipo'); // CONCENTRADOR, TANQUE, MASCARILLA, etc.
            $table->text('descripcion')->nullable();
            $table->string('estado_equipo')->default('DISPONIBLE'); // DISPONIBLE, EN_USO, MANTENIMIENTO, RETIRADO
            $table->date('fecha_adquisicion');
            $table->date('fecha_ultimo_mantenimiento')->nullable();
            $table->date('fecha_proximo_mantenimiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('stamp_user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos_oxigenoterapia');
    }
}; 