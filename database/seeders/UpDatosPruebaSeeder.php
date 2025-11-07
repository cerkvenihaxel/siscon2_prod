<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UpConsumo;
use Carbon\Carbon;

class UpDatosPruebaSeeder extends Seeder
{
    /**
     * Crear datos de prueba para el circuito completo UP
     */
    public function run()
    {
        $this->command->info('Creando datos de prueba para Unión Personal...');

        // Datos base de afiliados de testing
        $afiliados = [
            [
                'codigo' => '54715500',
                'apellidos' => 'PRUEBA',
                'nombres' => 'AFILIADO PRUEBA',
                'plan' => '150',
                'plan_nombre' => 'PLAN ACCORD 150',
                'edad' => 35,
                'tipo_afiliado' => 'VOL',
            ],
            [
                'codigo' => '54715300',
                'apellidos' => 'PRUEBA1',
                'nombres' => 'AFILIADO PRUEBA1',
                'plan' => '2',
                'plan_nombre' => 'PLAN BASICO',
                'edad' => 42,
                'tipo_afiliado' => 'OBL',
            ]
        ];

        // Prestaciones de testing
        $prestaciones = [
            [
                'codigo' => '1420107',
                'descripcion' => 'CONSULTA ESPECIALIZADA',
                'tipo' => 'P',
                'importe' => 100.00
            ],
            [
                'codigo' => '1420101',
                'descripcion' => 'CONSULTA MEDICA',
                'tipo' => 'P',
                'importe' => 80.00
            ]
        ];

        $consumosCreados = 0;

        foreach ($afiliados as $afiliado) {
            foreach ($prestaciones as $prestacion) {
                // Crear consumos en diferentes estados para testing
                $estados = ['pendiente', 'elegibilidad_ok', 'aprobado'];
                
                foreach ($estados as $index => $estado) {
                    $fechaBase = Carbon::now()->subDays($index + 1);
                    
                    $consumo = UpConsumo::create([
                        'afiliado' => $afiliado['codigo'],
                        'apellidos' => $afiliado['apellidos'],
                        'nombres' => $afiliado['nombres'],
                        'modelo_plan' => $afiliado['plan'],
                        'nombre_modelo_plan' => $afiliado['plan_nombre'],
                        'codigopostal' => '5000',
                        'localidad' => 'CORDOBA',
                        'provincia' => 'CORDOBA',
                        'edad' => $afiliado['edad'],
                        'tipo_afiliado' => $afiliado['tipo_afiliado'],
                        'titofam' => 'T',
                        'fecha_tran' => $fechaBase,
                        'prestacion' => $prestacion['descripcion'],
                        'tipo_pres' => $prestacion['tipo'],
                        'cod_prestacion' => $prestacion['codigo'],
                        'cant' => 1,
                        'desc' => $prestacion['descripcion'],
                        'status' => 'OK',
                        'cargo' => 0,
                        'impos' => $prestacion['importe'],
                        'impot' => 0,
                        'imptot' => $prestacion['importe'],
                        'adic' => 0,
                        'nombre_efector' => 'GLOBAL MEDICA SA',
                        'emisor_app' => 'SISCON2_TEST',
                        'num_tran' => 'TEST_' . time() . '_' . $consumosCreados,
                        'cod_prestador' => '8888',
                        'consultorio_prestador' => '001',
                        'efector_nombre' => 'GLOBAL MEDICA SA',
                        'consultorio_nombre' => 'CONSULTORIO 1',
                        
                        // Estados del flujo
                        'estado_flujo' => $estado,
                        'idtran_elegibilidad' => $estado !== 'pendiente' ? 'TEST_ELG_' . time() . '_' . $consumosCreados : null,
                        'idtran_aprobacion' => $estado === 'aprobado' ? 'TEST_AP_' . time() . '_' . $consumosCreados : null,
                        'idaut' => $estado === 'aprobado' ? 'TEST_AUTH_' . (80000000 + $consumosCreados) : null,
                        'fecha_elegibilidad' => $estado !== 'pendiente' ? $fechaBase->copy()->addHours(1) : null,
                        'fecha_aprobacion' => $estado === 'aprobado' ? $fechaBase->copy()->addHours(2) : null,
                        'usuario_elegibilidad' => $estado !== 'pendiente' ? 'admin@siscon.com' : null,
                        'usuario_aprobacion' => $estado === 'aprobado' ? 'admin@siscon.com' : null,
                        'observaciones_flujo' => "Consumo de prueba - Estado: {$estado}",
                    ]);

                    $consumosCreados++;
                    $this->command->info("Creado consumo {$consumosCreados}: {$afiliado['codigo']} - {$prestacion['descripcion']} - {$estado}");
                }
            }
        }

        // Crear algunos consumos adicionales para casos especiales
        $this->crearCasosEspeciales($consumosCreados);

        $this->command->info("✅ Creados {$consumosCreados} consumos de prueba para testing completo");
    }

    private function crearCasosEspeciales($baseCount)
    {
        // Caso 1: Consumo entregado (flujo completo)
        UpConsumo::create([
            'afiliado' => '54715500',
            'apellidos' => 'PRUEBA',
            'nombres' => 'AFILIADO PRUEBA',
            'modelo_plan' => '150',
            'nombre_modelo_plan' => 'PLAN ACCORD 150',
            'codigopostal' => '5000',
            'localidad' => 'CORDOBA',
            'provincia' => 'CORDOBA',
            'edad' => 35,
            'tipo_afiliado' => 'VOL',
            'titofam' => 'T',
            'fecha_tran' => Carbon::now()->subDays(5),
            'prestacion' => 'CONSULTA ESPECIALIZADA',
            'tipo_pres' => 'P',
            'cod_prestacion' => '1420107',
            'cant' => 1,
            'desc' => 'CONSULTA ESPECIALIZADA',
            'status' => 'OK',
            'cargo' => 0,
            'impos' => 100,
            'impot' => 0,
            'imptot' => 100,
            'adic' => 0,
            'nombre_efector' => 'GLOBAL MEDICA SA',
            'emisor_app' => 'SISCON2_TEST',
            'num_tran' => 'TEST_COMPLETO_' . time(),
            'cod_prestador' => '8888',
            'estado_flujo' => 'entregado',
            'idtran_elegibilidad' => 'TEST_ELG_COMPLETO_' . time(),
            'idtran_aprobacion' => 'TEST_AP_COMPLETO_' . time(),
            'idaut' => 'TEST_AUTH_' . (90000000 + $baseCount),
            'fecha_elegibilidad' => Carbon::now()->subDays(5)->addHours(1),
            'fecha_aprobacion' => Carbon::now()->subDays(5)->addHours(2),
            'fecha_entrega' => Carbon::now()->subDays(5)->addHours(3),
            'usuario_elegibilidad' => 'admin@siscon.com',
            'usuario_aprobacion' => 'admin@siscon.com',
            'usuario_entrega' => 'admin@siscon.com',
            'observaciones_flujo' => 'Caso de prueba: Flujo completo exitoso',
        ]);

        // Caso 2: Consumo anulado
        UpConsumo::create([
            'afiliado' => '54715300',
            'apellidos' => 'PRUEBA1',
            'nombres' => 'AFILIADO PRUEBA1',
            'modelo_plan' => '2',
            'nombre_modelo_plan' => 'PLAN BASICO',
            'codigopostal' => '5000',
            'localidad' => 'CORDOBA',
            'provincia' => 'CORDOBA',
            'edad' => 42,
            'tipo_afiliado' => 'OBL',
            'titofam' => 'T',
            'fecha_tran' => Carbon::now()->subDays(3),
            'prestacion' => 'CONSULTA MEDICA',
            'tipo_pres' => 'P',
            'cod_prestacion' => '1420101',
            'cant' => 1,
            'desc' => 'CONSULTA MEDICA',
            'status' => 'OK',
            'cargo' => 0,
            'impos' => 80,
            'impot' => 0,
            'imptot' => 80,
            'adic' => 0,
            'nombre_efector' => 'GLOBAL MEDICA SA',
            'emisor_app' => 'SISCON2_TEST',
            'num_tran' => 'TEST_ANULADO_' . time(),
            'cod_prestador' => '8888',
            'estado_flujo' => 'anulado',
            'idtran_elegibilidad' => 'TEST_ELG_ANULADO_' . time(),
            'idtran_aprobacion' => 'TEST_AP_ANULADO_' . time(),
            'idaut' => 'TEST_AUTH_' . (95000000 + $baseCount),
            'fecha_elegibilidad' => Carbon::now()->subDays(3)->addHours(1),
            'fecha_aprobacion' => Carbon::now()->subDays(3)->addHours(2),
            'fecha_anulacion' => Carbon::now()->subDays(3)->addHours(4),
            'usuario_elegibilidad' => 'admin@siscon.com',
            'usuario_aprobacion' => 'admin@siscon.com',
            'usuario_anulacion' => 'admin@siscon.com',
            'observaciones_flujo' => 'Caso de prueba: Transacción anulada por error administrativo',
        ]);

        $this->command->info("✅ Creados casos especiales: entregado y anulado");
    }
}
