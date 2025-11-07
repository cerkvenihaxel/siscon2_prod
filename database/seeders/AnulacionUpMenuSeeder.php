<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnulacionUpMenuSeeder extends Seeder
{
    public function run()
    {
        // Buscar el menú padre "Convenio UP"
        $menuPadre = DB::table('cms_menus')->where('name', 'Convenio UP')->first();
        
        if ($menuPadre) {
            // Verificar si ya existe el menú
            $existeMenu = DB::table('cms_menus')
                ->where('name', 'Anulación UP')
                ->where('parent_id', $menuPadre->id)
                ->exists();
            
            if (!$existeMenu) {
                DB::table('cms_menus')->insert([
                    'name' => 'Anulación UP',
                    'type' => 'URL',
                    'path' => '/admin/anulacion-up',
                    'color' => 'normal',
                    'icon' => 'fa fa-ban',
                    'parent_id' => $menuPadre->id,
                    'is_active' => 1,
                    'is_dashboard' => 0,
                    'id_cms_privileges' => 1,
                    'sorting' => 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "✅ Menú 'Anulación UP' creado exitosamente\n";
            } else {
                echo "ℹ️ El menú 'Anulación UP' ya existe\n";
            }
        } else {
            echo "❌ No se encontró el menú padre 'Convenio UP'\n";
        }
    }
}
