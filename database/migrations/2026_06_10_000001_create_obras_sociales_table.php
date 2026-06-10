<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo de obras sociales (escalable multi-OS).
 *
 * Vive en la conexión "drogueria" porque sus filas (osplad_consumos) la referencian
 * vía la columna os_id. Cada obra social mapea uno o más id_convenio de la droguería.
 */
return new class extends Migration
{
    protected $connection = 'drogueria';

    public function up(): void
    {
        if (Schema::connection('drogueria')->hasTable('obras_sociales')) {
            return;
        }

        Schema::connection('drogueria')->create('obras_sociales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo', 20)->unique()->comment('Código corto, ej: OSPLAD');
            $table->string('nombre', 150);
            $table->string('convenios', 100)->nullable()
                  ->comment('id_convenio de droguería asociados, separados por coma. Ej: 15,16');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Semilla inicial: OSPLAD (convenios 15 y 16 según drogueria.osplad_consumos)
        DB::connection('drogueria')->table('obras_sociales')->insert([
            'codigo'     => 'OSPLAD',
            'nombre'     => 'OSPLAD',
            'convenios'  => '15,16',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::connection('drogueria')->dropIfExists('obras_sociales');
    }
};
