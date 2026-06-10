<?php

namespace App\Http\Middleware;

use Closure;
use CRUDBooster;
use Illuminate\Http\Request;

/**
 * Control de acceso para el flujo de obras sociales (OSPLAD y futuras).
 *
 * - Super Admin y Administrador General: acceso total (ven todas las farmacias).
 * - Privilegios de farmacia (ej: "Farmacias OSPLAD"): acceso, ven solo su id_cliente.
 * - Resto: denegado.
 *
 * Los privilegios de farmacia habilitados se listan abajo; agregar nuevos según
 * se sumen obras sociales.
 */
class ObraSocialMiddleware
{
    private array $privilegiosFarmacia = [
        'Farmacias OSPLAD',
        'Farmacias UP',
        'Farmacias',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath('login'));
        }

        if (CRUDBooster::isSuperadmin() || CRUDBooster::myPrivilegeName() === 'Administrador General') {
            return $next($request);
        }

        if (in_array(CRUDBooster::myPrivilegeName(), $this->privilegiosFarmacia, true)) {
            return $next($request);
        }

        return redirect(CRUDBooster::adminPath())
            ->with('message', 'No tiene permisos para acceder a esta sección');
    }
}
