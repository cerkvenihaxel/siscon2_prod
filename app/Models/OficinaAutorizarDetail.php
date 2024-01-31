<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficinaAutorizarDetail extends Model
{
    use HasFactory;
    protected $table = 'convenio_oficina_os_detail'; // <--- Tabla para la carga de proveedores con sus respectivos medicamentos

    public function oficinaAutorizar()
    {
        return $this->belongsTo(OficinaAutorizar::class, 'convenio_oficina_os_id');
    }

}
