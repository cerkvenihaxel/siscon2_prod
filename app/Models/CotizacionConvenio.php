<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionConvenio extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_convenio';

    public function index()
    {
        return view('validadorFarmacia');
    }

    public function oficinaAutorizarDetail()
    {
        return $this->hasMany(CotizacionConvenioDetail::class, 'cotizacion_convenio_id');
    }
}
