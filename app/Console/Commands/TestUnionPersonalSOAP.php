<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UnionPersonalSoapService;
use App\Models\AfiliadoConvenioUp;
use App\Models\UpSolicitud;
use App\Models\UpSolicitudItem;

class TestUnionPersonalSOAP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'up:test {action=all}
                            {--afiliado=54715500 : Código de afiliado de prueba}
                            {--token=9999 : TOKEN de prueba}
                            {--plan=150 : Plan de prueba}
                            {--vercred=45 : Versión de credencial}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la integración SOAP con Unión Personal';

    private $soapService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->soapService = new UnionPersonalSoapService();

        $action = $this->argument('action');

        $this->info('🧪 Test de Integración SOAP - Unión Personal');
        $this->info('===============================================');
        $this->newLine();

        switch ($action) {
            case 'test':
                return $this->testConnection();
            case 'elg':
                return $this->testElegibilidad();
            case 'ap':
                return $this->testAutorizacion();
            case 'atr':
                return $this->testAnulacion();
            case 'flujo':
                return $this->testFlujoCompleto();
            case 'all':
            default:
                $this->testConnection();
                $this->newLine();
                $this->testElegibilidad();
                $this->newLine();
                $this->testStats();
                return 0;
        }
    }

    /**
     * Test 1: Conexión SOAP
     */
    private function testConnection()
    {
        $this->info('📡 Test 1: Conexión SOAP (TestWs)');
        $this->info('----------------------------------');

        try {
            $resultado = $this->soapService->testWs();

            if ($resultado['success']) {
                $this->info('✅ Conexión exitosa');
                $this->line('   Mensaje: ' . $resultado['message']);
            } else {
                $this->error('❌ Conexión fallida');
                $this->line('   Error: ' . $resultado['message']);
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Test 2: Elegibilidad (ELG) con autocreación de afiliado
     */
    private function testElegibilidad()
    {
        $this->info('🔍 Test 2: Elegibilidad (ELG) - Autocreación de Afiliado');
        $this->info('--------------------------------------------------------');

        $afiliado = $this->option('afiliado');
        $token = $this->option('token');

        $this->line("Afiliado: {$afiliado}");
        $this->line("TOKEN: {$token}");

        // Verificar si ya existe
        $existeAntes = AfiliadoConvenioUp::where('codigo_afiliado', $afiliado)->exists();
        $this->line('¿Existe antes?: ' . ($existeAntes ? 'SÍ' : 'NO'));

        try {
            $params = [
                'afiliado_codigo' => $afiliado,
                'token' => $token,
                'verifid' => 'MANUAL',
                'prestaciones' => [
                    [
                        'tipo' => 'P',
                        'id' => '1420107',
                        'cant' => 1,
                    ],
                ],
            ];

            $resultado = $this->soapService->ejecutarELG($params);

            if ($resultado['success']) {
                $this->info('✅ Elegibilidad verificada');
                $this->line('   IDTRAN: ' . ($resultado['idtran'] ?? 'N/A'));
                $this->line('   STATUS: ' . ($resultado['data']['STATUS'] ?? 'N/A'));
                $this->line('   Mensaje: ' . ($resultado['data']['RSPMSGG'] ?? 'N/A'));

                // Verificar si se autocreó el afiliado
                $this->newLine();
                $this->info('🌟 Verificando autocreación de afiliado...');

                $afiliadoCreado = AfiliadoConvenioUp::where('codigo_afiliado', $afiliado)->first();

                if ($afiliadoCreado) {
                    $this->info('   ✅ Afiliado ' . ($existeAntes ? 'actualizado' : 'creado') . ' automáticamente');
                    $this->line('   Código: ' . $afiliadoCreado->codigo_afiliado);
                    $this->line('   Nombre: ' . $afiliadoCreado->nombre_completo);
                    $this->line('   Plan: ' . $afiliadoCreado->plan . ' - ' . $afiliadoCreado->plan_nombre);
                    $this->line('   Última verificación: ' . $afiliadoCreado->ultima_verificacion_soap);
                } else {
                    $this->warn('   ⚠️  Afiliado NO se autocreó (verificar configuración)');
                }

                // Mostrar datos del afiliado desde SOAP
                if (isset($resultado['data']['AFICODIGO'])) {
                    $this->newLine();
                    $this->info('📋 Datos desde SOAP:');
                    $this->line('   Código: ' . ($resultado['data']['AFICODIGO'] ?? ''));
                    $this->line('   Apellido: ' . ($resultado['data']['AFIAPE'] ?? ''));
                    $this->line('   Nombre: ' . ($resultado['data']['AFINOM'] ?? ''));
                    $this->line('   Plan: ' . ($resultado['data']['AFIPLAN'] ?? '') . ' - ' . ($resultado['data']['AFIPLANNOM'] ?? ''));
                }

                // Mostrar prestaciones si existen
                if (isset($resultado['data']['PR']) && is_array($resultado['data']['PR'])) {
                    $this->newLine();
                    $this->info('💊 Prestaciones verificadas:');
                    foreach ($resultado['data']['PR'] as $pr) {
                        $this->line('   - ' . ($pr['ID'] ?? '') . ': ' . ($pr['DESCRIPCION'] ?? ''));
                        $this->line('     Status: ' . ($pr['STATUS'] ?? ''));
                        if (isset($pr['IMPTOTAL'])) {
                            $this->line('     Importe: $' . $pr['IMPTOTAL']);
                        }
                    }
                }

            } else {
                $this->error('❌ Elegibilidad rechazada');
                $this->line('   Mensaje: ' . $resultado['message']);
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Test 3: Estadísticas del sistema
     */
    private function testStats()
    {
        $this->info('📊 Estadísticas del Sistema');
        $this->info('---------------------------');

        try {
            $consumos = \App\Models\UpConsumo::count();
            $afiliados = AfiliadoConvenioUp::count();
            $solicitudes = UpSolicitud::count();
            $transacciones = \App\Models\UpTransaccionSoap::count();

            $this->table(
                ['Tabla', 'Registros'],
                [
                    ['Consumos históricos', $consumos],
                    ['Afiliados autocreados', $afiliados],
                    ['Solicitudes', $solicitudes],
                    ['Transacciones SOAP', $transacciones],
                ]
            );

            if ($afiliados > 0) {
                $this->newLine();
                $this->info('🌟 Últimos afiliados autocreados:');
                $ultimos = AfiliadoConvenioUp::orderBy('ultima_verificacion_soap', 'desc')->limit(5)->get();

                foreach ($ultimos as $afi) {
                    $this->line('   - ' . $afi->codigo_afiliado . ': ' . $afi->nombre_completo . ' (' . $afi->plan_nombre . ')');
                }
            }

            if ($transacciones > 0) {
                $this->newLine();
                $this->info('📡 Últimas transacciones SOAP:');
                $ultimas = \App\Models\UpTransaccionSoap::orderBy('created_at', 'desc')->limit(5)->get();

                foreach ($ultimas as $trans) {
                    $this->line('   - ' . $trans->transaction_type . ' | ' . $trans->status . ' | ' . $trans->created_at->format('d/m/Y H:i'));
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Error obteniendo estadísticas: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Test 4: Autorización de Prestación (AP)
     */
    private function testAutorizacion()
    {
        $this->info('✅ Test 3: Autorización de Prestación (AP)');
        $this->info('------------------------------------------');

        $afiliado = $this->option('afiliado');
        $token = $this->option('token');

        try {
            $params = [
                'afiliado_codigo' => $afiliado,
                'token' => $token,
                'contexto_tipo' => 'A',
                'prestaciones' => [
                    [
                        'tipo' => 'P',
                        'id' => '1420107',
                        'cant' => 1,
                    ],
                ],
            ];

            $resultado = $this->soapService->ejecutarAP($params);

            if ($resultado['success']) {
                $this->info('✅ Prestación autorizada');
                $this->line('   IDTRAN: ' . ($resultado['idtran'] ?? 'N/A'));
                $this->line('   IDAUT: ' . ($resultado['idaut'] ?? 'N/A'));
                $this->line('   Mensaje: ' . ($resultado['data']['RSPMSGG'] ?? 'N/A'));
            } else {
                $this->error('❌ Autorización rechazada');
                $this->line('   Mensaje: ' . $resultado['message']);
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Test 5: Flujo completo
     */
    private function testFlujoCompleto()
    {
        $this->info('🔄 Test: Flujo Completo (ELG → AP)');
        $this->info('----------------------------------');

        $this->testConnection();
        $this->newLine();
        $this->testElegibilidad();
        $this->newLine();

        if ($this->confirm('¿Desea continuar con la autorización (AP)?', false)) {
            $this->testAutorizacion();
        }

        $this->newLine();
        $this->testStats();

        return 0;
    }
}
