<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use CRUDBooster;

class FarmaciasUpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath('login'));
        }

        // Super Admin y Administrador General pueden acceder a todo
        if (CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeName() == 'Administrador General') {
            return $next($request);
        }

        // Verificar si tiene permiso "Farmacias UP"
        if (CRUDBooster::myPrivilegeName() != 'Farmacias UP') {
            // Si no tiene permiso, denegar acceso
            return redirect(CRUDBooster::adminPath())->with('message', 'No tiene permisos para acceder a esta sección');
        }

        // Si tiene permiso "Farmacias UP", permitir acceso
        return $next($request);
    }
}
