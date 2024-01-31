<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionConvenioDetail extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_convenio_detail';

    public function entranteConvenio(){
        return $this->belongsTo(CotizacionConvenio::class, 'id');
    }
}
