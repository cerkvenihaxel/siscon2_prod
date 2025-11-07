<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpTransaccionSoap extends Model
{
    protected $table = 'up_transacciones_soap';

    public $timestamps = false;

    protected $fillable = [
        'solicitud_id',
        'transaction_type',
        'msgid',
        'idtran',
        'idaut',
        'afiliado_codigo',
        'request_xml',
        'response_xml',
        'status',
        'response_code',
        'response_message',
        'response_data',
        'execution_time_ms',
        'soap_endpoint',
        'client_ip',
        'error_message',
        'error_trace',
        'created_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con la solicitud
     */
    public function solicitud()
    {
        return $this->belongsTo(UpSolicitud::class, 'solicitud_id');
    }

    /**
     * Relación con el afiliado
     */
    public function afiliado()
    {
        return $this->belongsTo(AfiliadoConvenioUp::class, 'afiliado_codigo', 'codigo_afiliado');
    }

    /**
     * Scope para filtrar por tipo de transacción
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('transaction_type', $tipo);
    }

    /**
     * Scope para transacciones exitosas
     */
    public function scopeExitosas($query)
    {
        return $query->where('status', 'OK');
    }

    /**
     * Scope para transacciones fallidas
     */
    public function scopeFallidas($query)
    {
        return $query->where('status', 'NO');
    }

    /**
     * Verificar si la transacción fue exitosa
     */
    public function fueExitosa()
    {
        return $this->status === 'OK';
    }

    /**
     * Crear registro de transacción
     */
    public static function registrar(array $data)
    {
        return self::create([
            'solicitud_id' => $data['solicitud_id'] ?? null,
            'transaction_type' => $data['transaction_type'],
            'msgid' => $data['msgid'] ?? null,
            'idtran' => $data['idtran'] ?? null,
            'idaut' => $data['idaut'] ?? null,
            'afiliado_codigo' => $data['afiliado_codigo'] ?? null,
            'request_xml' => $data['request_xml'] ?? null,
            'response_xml' => $data['response_xml'] ?? null,
            'status' => $data['status'] ?? null,
            'response_code' => $data['response_code'] ?? null,
            'response_message' => $data['response_message'] ?? null,
            'response_data' => $data['response_data'] ?? null,
            'execution_time_ms' => $data['execution_time_ms'] ?? null,
            'soap_endpoint' => $data['soap_endpoint'] ?? null,
            'client_ip' => request()->ip(),
            'error_message' => $data['error_message'] ?? null,
            'error_trace' => $data['error_trace'] ?? null,
            'created_at' => now(),
        ]);
    }
}
