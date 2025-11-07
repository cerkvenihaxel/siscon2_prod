<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumosUpV2MenuSeeder extends Seeder
{
    public function run()
    {
        // Buscar el menú padre "Convenio UP"
        $menuPadre = DB::table('cms_menus')->where('name', 'Convenio UP')->first();
        
        if ($menuPadre) {
            // Agregar la vista personalizada
            DB::table('cms_menus')->insert([
                'name' => 'Vista Personalizada (V2)',
                'type' => 'url',
                'path' => '/consumos_up_v2',
                'color' => 'normal',
                'icon' => 'fa fa-eye',
                'parent_id' => $menuPadre->id,
                'is_active' => 1,
                'is_dashboard' => 0,
                'id_cms_privileges' => 1,
                'sorting' => 2, // Después del dashboard
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "✅ Menú 'Vista Personalizada (V2)' agregado exitosamente\n";
        } else {
            echo "❌ No se encontró el menú padre 'Convenio UP'\n";
        }
    }
}
