<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'estado_solicitud';

    public function oficinaAutorizar(){
        return $this->hasMany(OficinaAutorizar::class, 'proveedor', 'id');
    }

    public function cotizacionConvenio(){
        return $this->hasMany(CotizacionConvenio::class, 'estado_solicitud_id', 'id');
    }
}
