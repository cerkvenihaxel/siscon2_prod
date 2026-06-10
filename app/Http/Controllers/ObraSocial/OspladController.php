<?php

namespace App\Http\Controllers\ObraSocial;

/**
 * Flujo de consumos de OSPLAD.
 *
 * Toda la lógica vive en FlowController; esta clase solo declara la identidad
 * de la obra social. Para agregar otra obra social, copiar esta clase cambiando
 * los tres valores y registrar sus rutas/menús.
 */
class OspladController extends FlowController
{
    protected function osCodigo(): string
    {
        return 'OSPLAD';
    }

    protected function rutaBase(): string
    {
        return '/admin/osplad';
    }

    protected function titulo(): string
    {
        return 'OSPLAD';
    }
}
