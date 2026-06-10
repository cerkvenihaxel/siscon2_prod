<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Registra en CrudBooster el menú del flujo OSPLAD y el privilegio "Farmacias OSPLAD".
 *
 * Idempotente: se puede correr varias veces sin duplicar.
 *   php artisan db:seed --class=Database\\Seeders\\OspladMenusSeeder
 */
class OspladMenusSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Privilegio "Farmacias OSPLAD"
        $privId = DB::table('cms_privileges')->where('name', 'Farmacias OSPLAD')->value('id');
        if (!$privId) {
            $privId = DB::table('cms_privileges')->insertGetId([
                'name'           => 'Farmacias OSPLAD',
                'is_superadmin'  => 0,
                'theme_color'    => 'skin-blue',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // 2) Menú padre "OSPLAD"
        $parent = DB::table('cms_menus')->where('name', 'OSPLAD')->where('type', 'Module')->first()
               ?? DB::table('cms_menus')->where('name', 'OSPLAD')->first();

        $parentId = $parent->id ?? DB::table('cms_menus')->insertGetId([
            'name'        => 'OSPLAD',
            'type'        => 'URL',
            'path'        => '/admin/osplad/pendientes',
            'color'       => '#1abc9c',
            'icon'        => 'fa fa-medkit',
            'parent_id'   => 0,
            'is_active'   => 1,
            'is_dashboard'=> 0,
            'id_cms_privileges' => 1,
            'sorting'     => 99,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 3) Submenús del flujo
        $items = [
            ['name' => 'PENDIENTES',  'path' => '/admin/osplad/pendientes', 'icon' => 'fa fa-clock-o',      'sorting' => 1],
            ['name' => 'En tránsito', 'path' => '/admin/osplad/transito',   'icon' => 'fa fa-truck',        'sorting' => 2],
            ['name' => 'Entregas',    'path' => '/admin/osplad/entregas',   'icon' => 'fa fa-check-circle', 'sorting' => 3],
        ];

        foreach ($items as $it) {
            $exists = DB::table('cms_menus')->where('path', $it['path'])->first();
            $menuId = $exists->id ?? DB::table('cms_menus')->insertGetId([
                'name'         => $it['name'],
                'type'         => 'URL',
                'path'         => $it['path'],
                'color'        => '#1abc9c',
                'icon'         => $it['icon'],
                'parent_id'    => $parentId,
                'is_active'    => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting'      => $it['sorting'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // 4) Permisos del menú para los privilegios que acceden
            $this->darPermisoMenu($menuId, [1, 50, $privId]); // 1=Super Admin, 50=Admin General
        }

        // Permiso del menú padre
        if (!isset($parent->id)) {
            $this->darPermisoMenu($parentId, [1, 50, $privId]);
        }
    }

    private function darPermisoMenu(int $menuId, array $privilegeIds): void
    {
        foreach (array_unique($privilegeIds) as $pid) {
            $exists = DB::table('cms_menus_privileges')
                ->where('id_cms_menus', $menuId)
                ->where('id_cms_privileges', $pid)
                ->exists();
            if (!$exists) {
                DB::table('cms_menus_privileges')->insert([
                    'id_cms_menus'      => $menuId,
                    'id_cms_privileges' => $pid,
                ]);
            }
        }
    }
}
