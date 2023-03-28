<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proveedores_convenio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('email');
            $table->string('web');
            $table->string('contacto');
            $table->string('telefono_contacto');
            $table->string('email_contacto');
            $table->string('observaciones');
            $table->string('convenio');
            $table->string('fecha_inicio');
            $table->string('fecha_fin');
            $table->string('tipo');
            $table->string('estado');
            $table->string('observaciones_convenio');
            $table->string('provincia');
            $table->string('localidad');
            $table->string('codigo_postal');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores_convenio');
    }
};
