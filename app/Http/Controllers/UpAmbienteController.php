<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UpAmbienteController extends Controller
{
    public function cambiarAmbiente(Request $request)
    {
        // Solo super administradores pueden cambiar ambiente
        if (!\CRUDBooster::isSuperadmin()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
        }

        try {
            $ambiente = $request->input('ambiente');
            
            if (!in_array($ambiente, ['test', 'produccion'])) {
                return response()->json(['success' => false, 'message' => 'Ambiente no válido']);
            }

            // Leer el archivo .env
            $envPath = base_path('.env');
            $envContent = File::get($envPath);

            // Actualizar la variable UP_AMBIENTE
            $envContent = preg_replace('/^UP_AMBIENTE=.*/m', "UP_AMBIENTE={$ambiente}", $envContent);

            // Escribir el archivo .env actualizado
            File::put($envPath, $envContent);

            // Limpiar cache de configuración
            \Artisan::call('config:clear');

            return response()->json([
                'success' => true, 
                'message' => "Ambiente cambiado a: {$ambiente}",
                'ambiente' => $ambiente
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al cambiar ambiente: ' . $e->getMessage()
            ]);
        }
    }

    public function getAmbienteActual()
    {
        // Solo super administradores pueden ver ambiente
        if (!\CRUDBooster::isSuperadmin()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
        }

        return response()->json([
            'ambiente' => config('union_personal.ambiente')
        ]);
    }
}
