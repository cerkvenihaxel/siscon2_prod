<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnulacionesListadoMenuSeeder extends Seeder
{
    public function run()
    {
        // Buscar el menú padre "Sistema UP"
        $menuPadre = DB::table('cms_menus')->where('name', 'Sistema UP')->first();
        
        if ($menuPadre) {
            // Verificar si ya existe el menú
            $existeMenu = DB::table('cms_menus')
                ->where('name', 'Anulaciones UP')
                ->where('parent_id', $menuPadre->id)
                ->exists();
            
            if (!$existeMenu) {
                DB::table('cms_menus')->insert([
                    'name' => 'Anulaciones UP',
                    'type' => 'Route',
                    'path' => 'AdminUpAnulacionesController@getIndex',
                    'color' => 'normal',
                    'icon' => 'fa fa-ban',
                    'parent_id' => $menuPadre->id,
                    'is_active' => 1,
                    'is_dashboard' => 0,
                    'id_cms_privileges' => 1,
                    'sorting' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "✅ Menú 'Anulaciones UP' creado en Sistema UP exitosamente\n";
            } else {
                echo "ℹ️ El menú 'Anulaciones UP' ya existe\n";
            }
        } else {
            echo "❌ No se encontró el menú padre 'Sistema UP'\n";
        }
    }
}
