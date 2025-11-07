<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use CRUDBooster;

class RedirectFarmaciasUp
{
    public function handle(Request $request, Closure $next)
    {
        // Solo aplicar en la ruta principal del admin
        if ($request->is('admin') || $request->is('admin/')) {
            // Si es usuario "Farmacias UP" (no Super Admin ni Administrador General)
            if (CRUDBooster::myPrivilegeName() == 'Farmacias UP') {
                return redirect('/admin/transaccion-ap');
            }
        }

        return $next($request);
    }
}
