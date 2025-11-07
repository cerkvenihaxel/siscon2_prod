<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpSolicitud extends Model
{
    protected $table = 'up_solicitudes';

    protected $fillable = [
        'nro_solicitud',
        'codigo_afiliado',
        'prestador_id',
        'prestador_nombre',
        'usrid',
        'usrpass',
        'estado',
        'msgid_elegibilidad',
        'idtran_elegibilidad',
        'msgid_aprobacion',
        'idtran_aprobacion',
        'idaut',
        'msgid_anulacion',
        'idtran_anulacion',
        'fecha_solicitud',
        'observaciones',
        'usuario_creador',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
    ];

    /**
     * Relación con el afiliado
     */
    public function afiliado()
    {
        return $this->belongsTo(AfiliadoConvenioUp::class, 'codigo_afiliado', 'codigo_afiliado');
    }

    /**
     * Relación con los ítems de la solicitud
     */
    public function items()
    {
        return $this->hasMany(UpSolicitudItem::class, 'solicitud_id');
    }

    /**
     * Relación con las transacciones SOAP
     */
    public function transacciones()
    {
        return $this->hasMany(UpTransaccionSoap::class, 'solicitud_id');
    }

    /**
     * Generar número de solicitud único
     */
    public static function generarNroSolicitud()
    {
        $prefix = 'UP';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));

        return $prefix . $timestamp . $random;
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para solicitudes aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Scope para solicitudes en borrador
     */
    public function scopeBorradores($query)
    {
        return $query->where('estado', 'borrador');
    }

    /**
     * Verificar si la solicitud puede ser editada
     */
    public function puedeSerEditada()
    {
        return in_array($this->estado, ['borrador', 'elegibilidad_error']);
    }

    /**
     * Verificar si la solicitud puede ser anulada
     */
    public function puedeSerAnulada()
    {
        return in_array($this->estado, ['aprobada', 'pendiente']) && !empty($this->idtran_aprobacion);
    }
}
