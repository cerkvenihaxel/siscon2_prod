<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficinaAutorizar extends Model
{
    use HasFactory;
    protected $table = 'convenio_oficina_os';

    public function afiliados(){
        return $this->belongsTo(Afiliados::class, 'afiliados_id', 'id');
    }

    public function medicos (){
        return $this->belongsTo(Medicos::class, 'medicos_id', 'id');
    }

    public function patologias(){
        return $this->belongsTo(Patologias::class, 'patologia', 'id');
    }

    public function estadoSolicitud(){
        return $this->belongsTo(EstadoSolicitud::class, 'estado_solicitud_id', 'id');
    }

    public function proveedoresConvenio(){
        return $this->belongsTo(ProveedoresConvenioMedicamentos::class, 'proveedor', 'id');
    }




}
