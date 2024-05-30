<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionConvenioDetail extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_convenio_detail';

    public function oficinaAutorizar(){
        return $this->belongsTo(CotizacionConvenio::class, 'cotizacion_convenio_id');
    }

    public function entranteConvenio(){
        return $this->belongsTo(CotizacionConvenio::class, 'id');
    }
}
