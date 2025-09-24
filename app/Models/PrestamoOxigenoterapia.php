<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoOxigenoterapia extends Model
{
    use HasFactory;

    protected $table = 'prestamo_oxigenoterapia';

    protected $fillable = [
        'pedido_oxigenoterapia_id',
        'nro_prestamo',
        'fecha_inicio_prestamo',
        'fecha_fin_prestamo',
        'fecha_entrega',
        'fecha_devolucion',
        'tipo_direccion',
        'direccion_entrega',
        'localidad_entrega',
        'provincia_entrega',
        'codigo_postal',
        'telefono_contacto',
        'nombre_contacto',
        'observaciones_entrega',
        'equipo_entregado',
        'nro_serie_equipo',
        'estado_prestamo',
        'stamp_user'
    ];

    protected $casts = [
        'fecha_inicio_prestamo' => 'date',
        'fecha_fin_prestamo' => 'date',
        'fecha_entrega' => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function pedidoOxigenoterapia()
    {
        return $this->belongsTo(PedidoOxigenoterapia::class, 'pedido_oxigenoterapia_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentosPrestamo::class, 'prestamo_oxigenoterapia_id');
    }

    public function equipo()
    {
        return $this->belongsTo(EquiposOxigenoterapia::class, 'nro_serie_equipo', 'nro_serie');
    }

    public function documentoTerminosCondiciones()
    {
        return $this->hasOne(DocumentosPrestamo::class, 'prestamo_oxigenoterapia_id')
                    ->where('tipo_documento', 'TERMINOS_CONDICIONES');
    }

    public function documentoContrato()
    {
        return $this->hasOne(DocumentosPrestamo::class, 'prestamo_oxigenoterapia_id')
                    ->where('tipo_documento', 'CONTRATO');
    }

    public function documentosRenovacion()
    {
        return $this->hasMany(DocumentosPrestamo::class, 'prestamo_oxigenoterapia_id')
                    ->where('tipo_documento', 'RENOVACION');
    }
} 