<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpConsumosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Importa el CSV de consumos a la tabla up_consumos
     *
     * @return void
     */
    public function run()
    {
        $csvPath = base_path('documentacion up/consumos up.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("❌ Archivo CSV no encontrado: {$csvPath}");
            return;
        }

        $this->command->info("📄 Importando consumos desde: {$csvPath}");

        // Leer CSV
        $file = fopen($csvPath, 'r');

        // Leer header
        $header = fgetcsv($file, 0, ';');

        if (!$header) {
            $this->command->error("❌ Error leyendo header del CSV");
            return;
        }

        $this->command->info("✅ Header: " . implode(', ', $header));

        $imported = 0;
        $errors = 0;

        DB::beginTransaction();

        try {
            // Limpiar tabla antes de importar (opcional)
            if ($this->command->confirm('¿Desea limpiar la tabla up_consumos antes de importar?', true)) {
                DB::table('up_consumos')->truncate();
                $this->command->info("🗑️  Tabla limpiada");
            }

            // Procesar filas
            while (($row = fgetcsv($file, 0, ';')) !== false) {
                try {
                    // Mapear columnas del CSV a campos de la tabla
                    $data = [
                        'afiliado' => $row[0] ?? null,
                        'apellidos' => $row[1] ?? null,
                        'nombres' => $row[2] ?? null,
                        'modelo_plan' => $row[3] ?? null,
                        'nombre_modelo_plan' => $row[4] ?? null,
                        'codigopostal' => $row[5] ?? null,
                        'localidad' => $row[6] ?? null,
                        'provincia' => $row[7] ?? null,
                        'edad' => !empty($row[8]) ? (int)$row[8] : null,
                        'tipo_afiliado' => $row[9] ?? null,
                        'titofam' => $row[10] ?? null,
                        'fecha_tran' => $this->parseFecha($row[11] ?? null),
                        'prestacion' => !empty($row[12]) ? (int)$row[12] : null,
                        'tipo_pres' => $row[13] ?? null,
                        'cod_prestacion' => $row[14] ?? null,
                        'cant' => !empty($row[15]) ? (int)$row[15] : null,
                        'desc' => $row[16] ?? null,
                        'status' => $row[17] ?? null,
                        'cargo' => $this->parseDecimal($row[18] ?? null),
                        'impos' => $this->parseDecimal($row[19] ?? null),
                        'impot' => $this->parseDecimal($row[20] ?? null),
                        'imptot' => $this->parseDecimal($row[21] ?? null),
                        'adic' => $this->parseDecimal($row[22] ?? null),
                        'nombre_efector' => $row[23] ?? null,
                        'emisor_app' => $row[24] ?? null,
                        'num_tran' => $row[25] ?? null,
                        'cod_prestador' => $row[26] ?? null,
                        'consultorio_prestador' => $row[27] ?? null,
                        'efector_nombre' => $row[28] ?? null,
                        'consultorio_nombre' => $row[29] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    DB::table('up_consumos')->insert($data);
                    $imported++;

                    if ($imported % 100 == 0) {
                        $this->command->info("📊 Importados: {$imported} registros...");
                    }

                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Error importando fila', [
                        'row' => $row,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            fclose($file);

            DB::commit();

            $this->command->info("✅ Importación completada:");
            $this->command->info("   ✔ Importados: {$imported} registros");
            $this->command->info("   ✖ Errores: {$errors} registros");

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);

            $this->command->error("❌ Error durante la importación: " . $e->getMessage());
            Log::error('Error en UpConsumosSeeder', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Parsear fecha desde formato CSV
     */
    private function parseFecha($fecha)
    {
        if (empty($fecha)) {
            return null;
        }

        try {
            // Formato esperado: 2025/10/20 00:00:00 o similar
            $fecha = trim($fecha);

            // Si viene en formato DD/MM/YYYY o YYYY/MM/DD
            if (preg_match('/^(\d{4})\/(\d{2})\/(\d{2})/', $fecha)) {
                return date('Y-m-d H:i:s', strtotime($fecha));
            }

            return date('Y-m-d H:i:s', strtotime($fecha));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parsear decimal
     */
    private function parseDecimal($value)
    {
        if (empty($value)) {
            return null;
        }

        // Eliminar separadores de miles y convertir
        $value = str_replace([',', ' '], '', trim($value));

        return (float)$value;
    }
}
