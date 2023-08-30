<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoMedicamento extends Model
{
    use HasFactory;

    protected $table = 'pedido_medicamento';

    public function detalles()
    {
        return $this->hasMany(PedidoMedicamentoDetail::class, 'pedido_medicamento_id');
    }

    public function afiliados()
    {
        return $this->belongsTo(Afiliados::class, 'afiliados_id', 'id');
    }

    public function medicos(){
        return $this->belongsTo(Medicos::class, 'medicos_id', 'id');
    }

    public function patologiaName(){
        return $this->belongsTo(Patologias::class, 'patologias', 'id');
    }

    public function estadoSolicitud(){
        return $this->belongsTo(EstadoSolicitud::class, 'estado_solicitud_id', 'id');
    }
}
