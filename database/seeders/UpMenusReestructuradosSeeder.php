<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpMenusReestructuradosSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Eliminar menús existentes de UP para evitar duplicados
        DB::table('cms_menus')->where('name', 'LIKE', '%UP%')->delete();
        DB::table('cms_menus')->where('name', 'LIKE', '%Unión Personal%')->delete();
        DB::table('cms_menus')->where('name', 'LIKE', '%Convenio UP%')->delete();

        // Menú principal: Convenio UP
        $menuPrincipalId = DB::table('cms_menus')->insertGetId([
            'name' => 'Convenio UP',
            'type' => 'module',
            'path' => '#',
            'color' => 'normal',
            'icon' => 'fa fa-handshake-o',
            'parent_id' => 0,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 15,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 1. Consultar Consumos UP (principal)
        DB::table('cms_menus')->insert([
            'name' => '1. Consultar Consumos UP',
            'type' => 'module',
            'path' => 'up_consumos',
            'color' => 'normal',
            'icon' => 'fa fa-search',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dashboard Farmacia
        DB::table('cms_menus')->insert([
            'name' => 'Dashboard Farmacia',
            'type' => 'route',
            'path' => 'AdminUpConsumosController@getDashboardFarmacia',
            'color' => 'normal',
            'icon' => 'fa fa-dashboard',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buscar por Afiliado
        DB::table('cms_menus')->insert([
            'name' => 'Buscar por Afiliado',
            'type' => 'route',
            'path' => 'AdminUpConsumosController@getBuscarPorAfiliado',
            'color' => 'normal',
            'icon' => 'fa fa-user-search',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Separador
        DB::table('cms_menus')->insert([
            'name' => '--- Historial de Transacciones ---',
            'type' => 'module',
            'path' => '#',
            'color' => 'normal',
            'icon' => 'fa fa-minus',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Elegibilidad (ELG)
        DB::table('cms_menus')->insert([
            'name' => '2. Elegibilidad (ELG)',
            'type' => 'module',
            'path' => 'up_elegibilidad',
            'color' => 'normal',
            'icon' => 'fa fa-user-check',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Aprobaciones (AP)
        DB::table('cms_menus')->insert([
            'name' => '3. Aprobaciones (AP)',
            'type' => 'module',
            'path' => 'up_autorizacion_previa',
            'color' => 'normal',
            'icon' => 'fa fa-check-circle',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Validaciones de Entrega (NUEVO)
        DB::table('cms_menus')->insert([
            'name' => '4. Validaciones de Entrega',
            'type' => 'module',
            'path' => 'up_validaciones_entrega',
            'color' => 'normal',
            'icon' => 'fa fa-clipboard-check',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Anulaciones (ATR)
        DB::table('cms_menus')->insert([
            'name' => '5. Anulaciones (ATR)',
            'type' => 'module',
            'path' => 'up_anulaciones',
            'color' => 'normal',
            'icon' => 'fa fa-times-circle',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Separador
        DB::table('cms_menus')->insert([
            'name' => '--- Gestión y Configuración ---',
            'type' => 'module',
            'path' => '#',
            'color' => 'normal',
            'icon' => 'fa fa-minus',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 9,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Afiliados UP
        DB::table('cms_menus')->insert([
            'name' => '6. Afiliados UP',
            'type' => 'module',
            'path' => 'afiliados_convenio_up',
            'color' => 'normal',
            'icon' => 'fa fa-users',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Transacciones SOAP
        DB::table('cms_menus')->insert([
            'name' => '7. Transacciones SOAP',
            'type' => 'module',
            'path' => 'up_transacciones_soap',
            'color' => 'normal',
            'icon' => 'fa fa-exchange',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 11,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Separador
        DB::table('cms_menus')->insert([
            'name' => '--- Reportes y Exportación ---',
            'type' => 'module',
            'path' => '#',
            'color' => 'normal',
            'icon' => 'fa fa-minus',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Exportar Validaciones
        DB::table('cms_menus')->insert([
            'name' => 'Exportar Validaciones',
            'type' => 'route',
            'path' => 'AdminUpConsumosController@getExportarValidaciones',
            'color' => 'normal',
            'icon' => 'fa fa-download',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 13,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Reportes Estadísticos
        DB::table('cms_menus')->insert([
            'name' => 'Reportes Estadísticos',
            'type' => 'route',
            'path' => 'AdminUpConsumosController@getReportesEstadisticos',
            'color' => 'normal',
            'icon' => 'fa fa-bar-chart',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 14,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Separador
        DB::table('cms_menus')->insert([
            'name' => '--- Ayuda y Configuración ---',
            'type' => 'module',
            'path' => '#',
            'color' => 'normal',
            'icon' => 'fa fa-minus',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 15,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Manual de Usuario
        DB::table('cms_menus')->insert([
            'name' => 'Manual de Usuario',
            'type' => 'route',
            'path' => 'ManualFlujoUpController@index',
            'color' => 'normal',
            'icon' => 'fa fa-book',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 16,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Configurar Ambiente
        DB::table('cms_menus')->insert([
            'name' => 'Configurar Ambiente',
            'type' => 'route',
            'path' => 'UpAmbienteController@index',
            'color' => 'normal',
            'icon' => 'fa fa-cogs',
            'parent_id' => $menuPrincipalId,
            'is_active' => 1,
            'is_dashboard' => 0,
            'id_cms_privileges' => 1,
            'sorting' => 17,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear también el controlador para validaciones de entrega si no existe
        $this->createValidacionesEntregaController();

        echo "✅ Menús de Convenio UP reestructurados creados exitosamente\n";
        echo "📂 Estructura del menú:\n";
        echo "   1. Consultar Consumos UP (principal)\n";
        echo "   - Dashboard Farmacia\n";
        echo "   - Buscar por Afiliado\n";
        echo "   2. Elegibilidad (ELG)\n";
        echo "   3. Aprobaciones (AP)\n";
        echo "   4. Validaciones de Entrega (NUEVO)\n";
        echo "   5. Anulaciones (ATR)\n";
        echo "   6. Afiliados UP\n";
        echo "   7. Transacciones SOAP\n";
        echo "   - Exportar Validaciones\n";
        echo "   - Reportes Estadísticos\n";
        echo "   - Manual de Usuario\n";
        echo "   - Configurar Ambiente\n";
    }

    /**
     * Crear entrada en cms_moduls para validaciones de entrega
     */
    private function createValidacionesEntregaController()
    {
        // Verificar si ya existe
        $exists = DB::table('cms_moduls')->where('table_name', 'up_validaciones_entrega')->exists();
        
        if (!$exists) {
            DB::table('cms_moduls')->insert([
                'name' => 'Validaciones de Entrega UP',
                'icon' => 'fa fa-clipboard-check',
                'path' => 'up_validaciones_entrega',
                'table_name' => 'up_validaciones_entrega',
                'controller' => 'AdminUpValidacionesEntregaController',
                'is_protected' => 0,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "✅ Módulo de Validaciones de Entrega creado\n";
        }
    }
}
