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

    public function estadoSolicitud()
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_solicitud_id', 'id');
    }

    public function estadoPedido(){
        return $this->belongsTo(EstadoPedido::class, 'estado_pedido_id', 'id');
    }
}
