<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoOxigenoterapia extends Model
{
    use HasFactory;

    protected $table = 'pedido_oxigenoterapia';

    protected $fillable = [
        'nro_solicitud',
        'afiliados_id',
        'nro_afiliado',
        'nombre_apellido',
        'documento',
        'edad',
        'clinicas_id',
        'medicos_id',
        'zona_residencia',
        'tel_afiliado',
        'email',
        'fecha_prescripcion',
        'fecha_vencimiento',
        'estado_oxigenoterapia_id',
        'tel_medico',
        'stamp_user',
        'observaciones',
        'archivo_prescripcion',
        'archivo_estudios',
        'archivo_otros'
    ];

    protected $casts = [
        'fecha_prescripcion' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function estadoOxigenoterapia()
    {
        return $this->belongsTo(EstadoOxigenoterapia::class, 'estado_oxigenoterapia_id');
    }

    public function afiliado()
    {
        return $this->belongsTo(Afiliados::class, 'afiliados_id');
    }

    public function medicos()
    {
        return $this->belongsTo(Medicos::class, 'medicos_id');
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinicas_id');
    }

    public function prestamos()
    {
        return $this->hasMany(PrestamoOxigenoterapia::class, 'pedido_oxigenoterapia_id');
    }

    public function prestamoActivo()
    {
        return $this->hasOne(PrestamoOxigenoterapia::class, 'pedido_oxigenoterapia_id')
                    ->where('estado_prestamo', 'ACTIVO');
    }

    /**
     * Relación con materiales/equipos
     */
    public function materiales()
    {
        return $this->hasMany(PedidoOxigenoterapiaMaterial::class, 'pedido_oxigenoterapia_id');
    }
} 