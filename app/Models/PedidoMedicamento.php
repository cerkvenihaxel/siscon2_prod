<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoMedicamento extends Model
{
    use HasFactory;

    protected $table = 'pedido_medicamento';
    protected $fillable = [
        'afiliados_id',
        'nroAfiliado',
        'edad',
        'nrosolicitud',
        'clinicas_id',
        'medicos_id',
        'zona_residencia',
        'tel_afiliado',
        'email',
        'fecha_receta',
        'postdatada',
        'fecha_vencimiento',
        'estado_solicitud_id',
        'tel_medico',
        'stamp_user',
        'discapacidad',
        'observaciones',
        'archivo',
        'archivo2',
        'archivo3',
        'archivo4',
        'obra_social',
        'provincia',
        'patologia',
        'diagnostico',
        'ant_postdatada',
        'ant_est_sol',
        'renovaciones'
    ];

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

    public function patologia(){
        return $this->belongsTo(Patologias::class, 'patologias', 'id');
    }

    public function estadoSolicitud(){
        return $this->belongsTo(EstadoSolicitud::class, 'estado_solicitud_id', 'id');
    }
}
