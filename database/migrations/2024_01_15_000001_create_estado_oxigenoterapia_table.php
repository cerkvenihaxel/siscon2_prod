<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_oxigenoterapia', function (Blueprint $table) {
            $table->id();
            $table->string('estado');
            $table->string('descripcion')->nullable();
            $table->string('color')->default('primary');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_oxigenoterapia');
    }
}; 