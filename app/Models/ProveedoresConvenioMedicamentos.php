<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedoresConvenioMedicamentos extends Model
{
    use HasFactory;

    protected $table = 'proveedores_convenio';

    public function oficinaAutorizar(){
        return $this->belongsTo(OficinaAutorizar::class, 'oficina_autorizar_id', 'id');
    }
}
