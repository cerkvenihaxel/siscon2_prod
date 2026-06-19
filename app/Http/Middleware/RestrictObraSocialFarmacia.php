<?php

namespace App\Http\Middleware;

use Closure;
use CRUDBooster;
use Illuminate\Http\Request;

/**
 * Confina a los usuarios "Farmacias OSPLAD" al módulo OSPLAD.
 *
 * Al loguearse aterrizan en /admin/osplad/pendientes y cualquier intento de
 * navegar a otra ruta del admin los devuelve al módulo. Solo ven PENDIENTES,
 * En tránsito y Entregas (los menús ya están restringidos por privilegio).
 *
 * Se aplica globalmente en el grupo 'web' pero solo actúa para ese perfil.
 */
class RestrictObraSocialFarmacia
{
    /** Privilegios confinados a su módulo de obra social (solo /admin/osplad/*). */
    private array $confinados = [
        'Farmacias OSPLAD'           => '/admin/osplad/pendientes',
        'Super Administrador OSPLAD' => '/admin/osplad/pendientes',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (CRUDBooster::myId()) {
            $privilegio = CRUDBooster::myPrivilegeName();

            if (isset($this->confinados[$privilegio])) {
                $destino  = $this->confinados[$privilegio];
                $permitido = $this->rutaPermitida($request);

                if (!$permitido) {
                    return redirect($destino);
                }
            }
        }

        return $next($request);
    }

    /**
     * Solo se permite el propio módulo OSPLAD y las rutas de sesión/login.
     * Las rutas que no son del admin pasan sin restricción.
     */
    private function rutaPermitida(Request $request): bool
    {
        // Fuera del panel admin: no restringimos (assets, públicas, etc.)
        if (!$request->is('admin') && !$request->is('admin/*')) {
            return true;
        }

        return $request->is('admin/osplad')
            || $request->is('admin/osplad/*')
            || $request->is('admin/login')   || $request->is('admin/login/*')
            || $request->is('admin/logout')  || $request->is('admin/logout/*');
    }
}
