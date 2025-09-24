<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreCompletoToEquiposOxigenoterapiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipos_oxigenoterapia', function (Blueprint $table) {
            $table->string('nombre_completo')->nullable()->after('nombre_equipo');
        });

        // Actualizar la columna con los datos combinados
        DB::statement("UPDATE equipos_oxigenoterapia SET nombre_completo = CONCAT(nombre_equipo, ' - ', marca, ' ', modelo)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipos_oxigenoterapia', function (Blueprint $table) {
            $table->dropColumn('nombre_completo');
        });
    }
}
