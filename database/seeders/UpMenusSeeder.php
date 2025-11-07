<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Registra los módulos de Unión Personal en el menú de CRUDBooster
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("📋 Registrando módulos de Unión Personal en CRUDBooster...");

        // Obtener el último sorting para agregar al final
        $ultimoSorting = DB::table('cms_menus')->max('sorting') ?? 0;

        // Verificar si ya existen los menús
        $existeConsumosUP = DB::table('cms_menus')->where('name', 'Consumos UP')->exists();
        $existeElegibilidadUP = DB::table('cms_menus')->where('name', 'ELG UP')->exists();

        if ($existeConsumosUP && $existeElegibilidadUP) {
            $this->command->warn("⚠️  Los módulos ya están registrados. Saltando...");
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Crear menú padre: "Unión Personal"
            $menuPadreId = DB::table('cms_menus')->insertGetId([
                'name' => 'Unión Personal',
                'type' => 'Header',
                'path' => null,
                'color' => 'normal',
                'icon' => 'fa fa-medkit',
                'parent_id' => 0,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => $ultimoSorting + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Menú padre creado: Unión Personal (ID: {$menuPadreId})");

            // 2. Submenu: Consumos UP
            $consumosMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'Consumos UP',
                'type' => 'Route',
                'path' => 'admin/up_consumos',
                'color' => 'normal',
                'icon' => 'fa fa-history',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: Consumos UP (ID: {$consumosMenuId})");

            // 3. Submenu: ELG UP (Elegibilidad)
            $elegibilidadMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'ELG UP',
                'type' => 'Route',
                'path' => 'admin/up_elegibilidad',
                'color' => 'normal',
                'icon' => 'fa fa-check-circle',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: ELG UP (ID: {$elegibilidadMenuId})");

            // 4. Submenu: AP UP (Autorización Previa)
            $autorizacionMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'AP UP',
                'type' => 'Route',
                'path' => 'admin/up_autorizacion_previa',
                'color' => 'normal',
                'icon' => 'fa fa-check',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: AP UP (ID: {$autorizacionMenuId})");

            // 5. Submenu: ATR UP (Anulaciones)
            $anulacionMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'ATR UP',
                'type' => 'Route',
                'path' => 'admin/up_anulaciones',
                'color' => 'normal',
                'icon' => 'fa fa-times-circle',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: ATR UP (ID: {$anulacionMenuId})");

            // 6. Submenu: Solicitudes UP
            $solicitudesMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'Solicitudes UP',
                'type' => 'Route',
                'path' => 'admin/up_solicitudes',
                'color' => 'normal',
                'icon' => 'fa fa-file-text',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: Solicitudes UP (ID: {$solicitudesMenuId})");

            // 7. Submenu: Afiliados UP (autocreados)
            $afiliadosMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'Afiliados UP',
                'type' => 'Route',
                'path' => 'admin/afiliados_convenio_up',
                'color' => 'normal',
                'icon' => 'fa fa-users',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: Afiliados UP (ID: {$afiliadosMenuId})");

            // 8. Submenu: Transacciones SOAP
            $transaccionesMenuId = DB::table('cms_menus')->insertGetId([
                'name' => 'Transacciones SOAP',
                'type' => 'Route',
                'path' => 'admin/up_transacciones_soap',
                'color' => 'normal',
                'icon' => 'fa fa-exchange',
                'parent_id' => $menuPadreId,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✅ Submenu creado: Transacciones SOAP (ID: {$transaccionesMenuId})");

            DB::commit();

            $this->command->newLine();
            $this->command->info("✅ ¡Módulos de Unión Personal registrados exitosamente!");
            $this->command->info("📍 Los módulos aparecerán en el menú de CRUDBooster bajo 'Unión Personal'");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error registrando módulos: " . $e->getMessage());
        }
    }
}
