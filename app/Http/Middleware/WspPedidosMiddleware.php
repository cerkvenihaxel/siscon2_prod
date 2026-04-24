<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CRUDBooster;

class WspPedidosMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath('login'));
        }

        // Superadmin tiene acceso siempre
        if (CRUDBooster::isSuperadmin()) {
            return $next($request);
        }

        $privilegeId = CRUDBooster::myPrivilegeId();

        // Buscar el módulo wsp_pedidos en cms_moduls
        $module = DB::table('cms_moduls')->where('path', 'wsp_pedidos')->first();

        if (!$module) {
            return $this->deny($request);
        }

        // Verificar que el perfil tenga is_visible=1 para este módulo
        $role = DB::table('cms_privileges_roles')
            ->where('id_cms_privileges', $privilegeId)
            ->where('id_cms_moduls', $module->id)
            ->where('is_visible', 1)
            ->first();

        if (!$role) {
            return $this->deny($request);
        }

        return $next($request);
    }

    private function deny(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'No tiene permisos para acceder a esta sección.'], 403);
        }

        return redirect(CRUDBooster::adminPath())
            ->with('message', 'No tiene permisos para acceder a Pedidos WhatsApp.')
            ->with('message_type', 'danger');
    }
}
