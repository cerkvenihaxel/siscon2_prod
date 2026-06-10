<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Importa farmacias de OSPLAD desde un CSV y crea/actualiza sus usuarios (cms_users)
 * con el privilegio "Farmacias OSPLAD" y una contraseña genérica.
 *
 * Pensado para ejecutarse en el servidor de producción:
 *   php artisan osplad:importar-farmacias                       # usa ./farmacias.csv
 *   php artisan osplad:importar-farmacias /ruta/farmacias.csv
 *   php artisan osplad:importar-farmacias --password=otraClave
 *   php artisan osplad:importar-farmacias --dry-run             # simula sin escribir
 *
 * Idempotente: si el email ya existe, actualiza datos/privilegio/id_cliente pero
 * NO pisa la contraseña existente. Solo crea contraseña al dar de alta.
 *
 * CSV esperado (encabezados, separador coma): incluye al menos
 *   "N° CLIENTE ZAFIRO", "FARMACIA", "CORREO ELECTRONICO".
 *   Opcionales: CUIT, PROVINCIA, LOCALIDAD, DOMICILIO, CODIGO POSTAL, TELEFONO.
 */
class OspladImportarFarmacias extends Command
{
    protected $signature = 'osplad:importar-farmacias
                            {file? : Ruta al CSV (default: base_path/farmacias.csv)}
                            {--password=farmaciaosplad2026 : Contraseña genérica para los nuevos usuarios}
                            {--privilegio=Farmacias OSPLAD : Nombre del privilegio CRUDBooster a asignar}
                            {--dry-run : Simula sin escribir en la base}';

    protected $description = 'Crea/actualiza usuarios de farmacias OSPLAD desde un CSV';

    public function handle(): int
    {
        $file = $this->argument('file') ?: base_path('farmacias.csv');
        $password = (string) $this->option('password');
        $dryRun = (bool) $this->option('dry-run');

        if (!is_file($file) || !is_readable($file)) {
            $this->error("No se encuentra o no se puede leer el archivo: {$file}");
            return self::FAILURE;
        }

        $privId = $this->resolverPrivilegio((string) $this->option('privilegio'), $dryRun);
        if ($privId === null) {
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $this->error("No se pudo abrir el archivo: {$file}");
            return self::FAILURE;
        }

        // Encabezado -> índices de columnas
        $header = fgetcsv($handle);
        if ($header === false) {
            $this->error('El CSV está vacío.');
            fclose($handle);
            return self::FAILURE;
        }
        // Quitar BOM del primer encabezado
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $col = $this->mapearColumnas($header);

        if (!isset($col['email']) || !isset($col['id_cliente']) || !isset($col['nombre'])) {
            $this->error('Faltan columnas obligatorias en el CSV (FARMACIA, N° CLIENTE ZAFIRO, CORREO ELECTRONICO).');
            fclose($handle);
            return self::FAILURE;
        }

        $hash = Hash::make($password);
        $creados = 0; $actualizados = 0; $omitidos = 0; $linea = 1;
        $omitidasDetalle = [];

        while (($row = fgetcsv($handle)) !== false) {
            $linea++;
            if ($this->filaVacia($row)) {
                continue;
            }

            $nombre    = trim((string) ($row[$col['nombre']] ?? ''));
            $idCliente = $this->soloDigitos($row[$col['id_cliente']] ?? '');
            $email     = $this->primerEmail($row[$col['email']] ?? '');

            if ($email === null) {
                $omitidos++;
                $omitidasDetalle[] = "L{$linea}: " . ($nombre ?: 'sin nombre') . ' (sin email válido)';
                continue;
            }
            if ($idCliente === null) {
                $omitidos++;
                $omitidasDetalle[] = "L{$linea}: {$nombre} ({$email}) — sin N° cliente Zafiro";
                continue;
            }

            $perfil = [
                'cuil_cuit'    => isset($col['cuit']) ? trim((string) ($row[$col['cuit']] ?? '')) : null,
                'provincia'    => isset($col['provincia']) ? trim((string) ($row[$col['provincia']] ?? '')) : null,
                'departamento' => isset($col['localidad']) ? trim((string) ($row[$col['localidad']] ?? '')) : null,
                'domicilio'    => isset($col['domicilio']) ? trim((string) ($row[$col['domicilio']] ?? '')) : null,
                'telefono'     => isset($col['telefono']) ? trim((string) ($row[$col['telefono']] ?? '')) : null,
                'codigo_postal'=> isset($col['cp']) ? ($this->soloDigitos($row[$col['cp']] ?? '')) : null,
            ];
            $perfil = array_filter($perfil, fn($v) => $v !== null && $v !== '');

            $existente = DB::table('cms_users')->where('email', $email)->first();

            if ($existente) {
                $update = array_merge($perfil, [
                    'name'              => $nombre ?: $existente->name,
                    'id_cms_privileges' => $privId,
                    'id_cliente'        => $idCliente,
                    'status'            => $existente->status ?: 'Active',
                    'updated_at'        => now(),
                ]);
                if (!$dryRun) {
                    DB::table('cms_users')->where('id', $existente->id)->update($update);
                }
                $actualizados++;
                $this->line("  ~ Actualizado: {$email}  (cliente {$idCliente})");
            } else {
                $insert = array_merge($perfil, [
                    'name'              => $nombre ?: $email,
                    'email'             => $email,
                    'password'          => $hash,
                    'id_cms_privileges' => $privId,
                    'id_cliente'        => $idCliente,
                    'status'            => 'Active',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
                if (!$dryRun) {
                    DB::table('cms_users')->insert($insert);
                }
                $creados++;
                $this->line("  + Creado:      {$email}  (cliente {$idCliente})");
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Listo. Creados: {$creados} | Actualizados: {$actualizados} | Omitidos: {$omitidos}");
        if (!empty($omitidasDetalle)) {
            $this->warn('Filas omitidas:');
            foreach ($omitidasDetalle as $d) {
                $this->line('   - ' . $d);
            }
        }
        if ($creados > 0 && !$dryRun) {
            $this->info("Contraseña genérica para los nuevos usuarios: \"{$password}\"");
        }

        return self::SUCCESS;
    }

    /** Asegura el privilegio y devuelve su id (lo crea si no existe). */
    private function resolverPrivilegio(string $nombre, bool $dryRun): ?int
    {
        $id = DB::table('cms_privileges')->where('name', $nombre)->value('id');
        if ($id) {
            return (int) $id;
        }
        if ($dryRun) {
            $this->warn("[DRY-RUN] El privilegio '{$nombre}' no existe; se crearía.");
            return 0; // placeholder para simular
        }
        $id = DB::table('cms_privileges')->insertGetId([
            'name'          => $nombre,
            'is_superadmin' => 0,
            'theme_color'   => 'skin-blue',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $this->info("Privilegio '{$nombre}' creado (id {$id}).");
        return (int) $id;
    }

    /** Mapea encabezados del CSV a claves internas, tolerante a mayúsculas/acentos. */
    private function mapearColumnas(array $header): array
    {
        $map = [];
        foreach ($header as $i => $h) {
            $u = mb_strtoupper(trim((string) $h));
            if (strpos($u, 'CLIENTE') !== false && strpos($u, 'ZAFIRO') !== false) $map['id_cliente'] = $i;
            elseif ($u === 'FARMACIA')                                            $map['nombre'] = $i;
            elseif (strpos($u, 'CORREO') !== false)                               $map['email'] = $i;
            elseif ($u === 'CUIT')                                                $map['cuit'] = $i;
            elseif (strpos($u, 'PROVINCIA') !== false)                            $map['provincia'] = $i;
            elseif (strpos($u, 'LOCALIDAD') !== false)                            $map['localidad'] = $i;
            elseif (strpos($u, 'DOMICILIO') !== false)                            $map['domicilio'] = $i;
            elseif (strpos($u, 'POSTAL') !== false)                               $map['cp'] = $i;
            elseif (strpos($u, 'TELEFON') !== false || strpos($u, 'TELÉFON') !== false) $map['telefono'] = $i;
        }
        return $map;
    }

    /** Devuelve el primer email válido de un campo que puede traer varios (separados por / , ;). */
    private function primerEmail($valor): ?string
    {
        $valor = (string) $valor;
        $partes = preg_split('/[\/,;\s]+/', $valor, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($partes as $p) {
            $p = trim(strtolower($p));
            if (filter_var($p, FILTER_VALIDATE_EMAIL)) {
                return $p;
            }
        }
        return null;
    }

    private function soloDigitos($valor): ?string
    {
        $d = preg_replace('/\D+/', '', (string) $valor);
        return $d === '' ? null : $d;
    }

    private function filaVacia(array $row): bool
    {
        foreach ($row as $c) {
            if (trim((string) $c) !== '') return false;
        }
        return true;
    }
}
