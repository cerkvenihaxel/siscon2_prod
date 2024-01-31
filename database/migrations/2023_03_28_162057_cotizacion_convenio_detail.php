<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotizacion_convenio_detail', function (Blueprint $table) {
            $table->id();

            $table->integer('cotizacion_convenio_id')->nullable();
            $table->integer('articuloZafiro_id')->nullable();
            $table->string('laboratorio')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('presentacion')->nullable();
            $table->string('precio')->nullable();
            $table->string('descuento')->nullable();
            $table->string('total')->nullable();
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_convenio_detail');
    }
};
