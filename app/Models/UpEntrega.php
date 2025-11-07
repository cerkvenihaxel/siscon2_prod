<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpEntrega extends Model
{
    protected $table = 'up_entregas';

    protected $fillable = [
        'consumo_id',
        'codigo_afiliado',
        'nombre_afiliado',
        'cod_prestacion',
        'descripcion_prestacion',
        'cantidad_solicitada',
        'cantidad_entregada',
        'fecha_entrega',
        'estado_entrega',
        'observaciones',
        'usuario_entrega',
        'archivo_consentimiento',
        'archivos_adjuntos',
        'firma_afiliado',
        'dni_afiliado',
        'remito',
        'transporte',
        'quien_recibe',
        'relacion_afiliado',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'archivos_adjuntos' => 'array',
    ];

    /**
     * Relación con consumo
     */
    public function consumo()
    {
        return $this->belongsTo(UpConsumo::class, 'consumo_id');
    }
}
