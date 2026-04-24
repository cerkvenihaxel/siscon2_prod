<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WspImportarPedidos extends Command
{
    protected $signature   = 'wsp:importar-pedidos {--path= : Ruta a los JSON (por defecto usa WSP_PEDIDOS_PATH del .env)}';
    protected $description = 'Importa pedidos de WhatsApp Agent desde archivos JSON a la BD';

    public function handle(): int
    {
        $dir = $this->option('path') ?: env('WSP_PEDIDOS_PATH');

        if (!$dir || !is_dir($dir)) {
            $this->error("Directorio no encontrado: {$dir}");
            $this->line("Configurá WSP_PEDIDOS_PATH en el .env o usá --path=<ruta>");
            return 1;
        }

        $archivos = glob("{$dir}/*.json");
        $this->info("Encontrados: " . count($archivos) . " archivos");

        $nuevos = $omitidos = $errores = 0;

        foreach ($archivos as $archivo) {
            try {
                $data = json_decode(file_get_contents($archivo), true);
                if (!$data || empty($data['id'])) {
                    $errores++;
                    continue;
                }

                $pedidoId = $data['id'];

                if (DB::table('wsp_pedidos')->where('pedido_id', $pedidoId)->exists()) {
                    $omitidos++;
                    continue;
                }

                $medicamentos = $data['medicamentos'] ?? [];
                if (empty($medicamentos) && isset($data['medicamento'])) {
                    $medicamentos = [$data['medicamento']];
                }

                DB::table('wsp_pedidos')->insert([
                    'pedido_id'      => $pedidoId,
                    'usuario_id'     => $data['usuario_id'] ?? $this->extraerUsuarioId($pedidoId),
                    'sucursal_id'    => $data['sucursal_id'] ?? null,
                    'cliente_nombre' => $data['cliente']['nombre'] ?? null,
                    'cliente_dni'    => $data['cliente']['dni'] ?? null,
                    'medicamentos'   => json_encode($medicamentos),
                    'total'          => (float)($data['total'] ?? 0),
                    'tipo_envio'     => $data['tipo_envio'] ?? null,
                    'ubicacion'      => $data['ubicacion'] ?? null,
                    'metodo_pago'    => $data['metodo_pago'] ?? null,
                    'estado'         => $data['estado'] ?? 'CONFIRMADO',
                    'pedido_at'      => isset($data['timestamp']) ? date('Y-m-d H:i:s', strtotime($data['timestamp'])) : null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $nuevos++;
                $this->line("  ✓ {$pedidoId}");

            } catch (\Exception $e) {
                $errores++;
                $this->warn("  ✗ " . basename($archivo) . ": " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Importados: {$nuevos} | Omitidos (ya existían): {$omitidos} | Errores: {$errores}");
        return 0;
    }

    private function extraerUsuarioId(string $pedidoId): ?string
    {
        // PED-20260331161224-5437 → "5437"
        $parts = explode('-', $pedidoId);
        return count($parts) >= 3 ? end($parts) : null;
    }
}
