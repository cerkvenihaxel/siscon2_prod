<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfiliadosConvenioUpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afiliados_convenio_up', function (Blueprint $table) {
            $table->id();

            // Datos básicos del afiliado
            $table->string('codigo_afiliado', 20)->unique()->comment('Código de afiliado UP');
            $table->string('apellido', 100)->nullable();
            $table->string('nombre', 100)->nullable();

            // Datos del plan
            $table->string('plan', 20)->nullable()->comment('Código del plan');
            $table->string('plan_nombre', 100)->nullable()->comment('Nombre del plan');

            // Datos personales
            $table->char('sexo', 1)->nullable()->comment('M/F');
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('edad')->nullable();

            // Ubicación
            $table->string('codigo_postal', 10)->nullable();
            $table->string('localidad', 100)->nullable();
            $table->string('provincia', 100)->nullable();

            // Clasificación
            $table->string('tipo_afiliado', 20)->nullable()->comment('OBL/VOL');
            $table->string('titofam', 10)->nullable()->comment('TIT/FAM');

            // Credenciales
            $table->string('ultima_version_credencial', 10)->nullable()->comment('Última versión de credencial conocida');

            // Metadatos
            $table->timestamp('ultima_verificacion_soap')->nullable()->comment('Última vez que se verificó por SOAP');
            $table->timestamps();

            // Índices
            $table->index('codigo_afiliado', 'idx_codigo_afiliado');
            $table->index('apellido', 'idx_apellido');
            $table->index(['apellido', 'nombre'], 'idx_nombre_completo');
            $table->index('plan', 'idx_plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('afiliados_convenio_up');
    }
}
