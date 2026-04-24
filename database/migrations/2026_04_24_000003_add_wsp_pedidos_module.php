<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MODULE_PATH = 'wsp_pedidos';

    // Privilegios con acceso por defecto: Super Administrator, Admin, Administrador General
    private const DEFAULT_PRIVILEGE_IDS = [1, 3, 50];

    public function up(): void
    {
        // Insertar módulo si no existe
        $existing = DB::table('cms_moduls')->where('path', self::MODULE_PATH)->first();

        if (!$existing) {
            $moduleId = DB::table('cms_moduls')->insertGetId([
                'name' => 'Pedidos WhatsApp',
                'path' => self::MODULE_PATH,
                'icon' => 'fa fa-whatsapp',
            ]);
        } else {
            $moduleId = $existing->id;
        }

        // Asignar is_visible=1 a los perfiles autorizados por defecto
        foreach (self::DEFAULT_PRIVILEGE_IDS as $privId) {
            $exists = DB::table('cms_privileges_roles')
                ->where('id_cms_privileges', $privId)
                ->where('id_cms_moduls', $moduleId)
                ->exists();

            if (!$exists) {
                DB::table('cms_privileges_roles')->insert([
                    'is_visible'       => 1,
                    'is_create'        => 1,
                    'is_read'          => 1,
                    'is_edit'          => 1,
                    'is_delete'        => 0,
                    'id_cms_privileges' => $privId,
                    'id_cms_moduls'    => $moduleId,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $module = DB::table('cms_moduls')->where('path', self::MODULE_PATH)->first();
        if ($module) {
            DB::table('cms_privileges_roles')->where('id_cms_moduls', $module->id)->delete();
            DB::table('cms_moduls')->where('id', $module->id)->delete();
        }
    }
};
