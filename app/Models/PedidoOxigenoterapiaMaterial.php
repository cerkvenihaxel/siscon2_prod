<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoOxigenoterapiaMaterial extends Model
{
    use HasFactory;

    protected $table = 'pedido_oxigenoterapia_materiales';

    protected $fillable = [
        'pedido_oxigenoterapia_id',
        'equipos_oxigenoterapia_id',
        'nombre_equipo',
        'codigo_equipo',
        'cantidad',
        'observaciones',
        'entregable',
        'observaciones_entrega'
    ];

    /**
     * Relación con el pedido
     */
    public function pedido()
    {
        return $this->belongsTo(PedidoOxigenoterapia::class, 'pedido_oxigenoterapia_id');
    }

    /**
     * Relación con el equipo
     */
    public function equipo()
    {
        return $this->belongsTo(EquiposOxigenoterapia::class, 'equipos_oxigenoterapia_id');
    }
} 