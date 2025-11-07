<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpAnulacion extends Model
{
    protected $table = 'up_anulaciones';

    protected $fillable = [
        'codigo_afiliado',
        'plan',
        'vercred',
        'token',
        'usrid',
        'usrpass',
        'tipoidanul',
        'idanul',
        'motivo',
        'fecha_anulacion',
        'msgid',
        'idtran',
        'status',
        'response_code',
        'response_message',
        'afi_codigo',
        'afi_apellido',
        'afi_nombre',
        'idaut_anulado',
        'response_data',
        'execution_time_ms',
        'usuario_creador',
        'observaciones',
    ];

    protected $casts = [
        'fecha_anulacion' => 'date',
        'response_data' => 'array',
        'execution_time_ms' => 'integer',
    ];

    /**
     * Relación con transacciones SOAP
     */
    public function transacciones()
    {
        return $this->hasMany(UpTransaccionSoap::class, 'msgid', 'msgid');
    }

    /**
     * Crear registro desde respuesta SOAP
     */
    public static function crearDesdeRespuestaSOAP(array $params, array $resultado)
    {
        $startTime = $params['start_time'] ?? null;
        $executionTime = $startTime ? (microtime(true) - $startTime) * 1000 : null;

        $data = [
            'codigo_afiliado' => $params['afiliado_codigo'] ?? null,
            'plan' => $params['plan'] ?? null,
            'vercred' => $params['vercred'] ?? null,
            'token' => $params['token'] ?? null,
            'usrid' => $params['usrid'] ?? config('union_personal.user_id'),
            'usrpass' => $params['usrpass'] ?? config('union_personal.user_pass'),
            'tipoidanul' => $params['tipoidanul'] ?? 'IDTRAN',
            'idanul' => $params['idanul'] ?? null,
            'motivo' => $params['motivo'] ?? null,
            'fecha_anulacion' => $params['fecha'] ?? now()->format('Y-m-d'),
            'msgid' => $params['msgid'] ?? null,
            'idtran' => $resultado['idtran'] ?? null,
            'status' => $resultado['success'] ? ($resultado['data']['STATUS'] ?? 'ERROR') : 'ERROR',
            'response_code' => $resultado['data']['RSPCODG'] ?? null,
            'response_message' => $resultado['message'] ?? null,
            'response_data' => $resultado['data'] ?? null,
            'execution_time_ms' => $executionTime,
            'usuario_creador' => \CRUDBooster::myName(),
        ];

        // Datos del afiliado
        if (isset($resultado['data']['AFICODIGO'])) {
            $data['afi_codigo'] = $resultado['data']['AFICODIGO'];
            $data['afi_apellido'] = $resultado['data']['AFIAPE'] ?? null;
            $data['afi_nombre'] = $resultado['data']['AFINOM'] ?? null;
        }

        // ID de autorización anulado
        if (isset($resultado['data']['IDAUT'])) {
            $data['idaut_anulado'] = $resultado['data']['IDAUT'];
        }

        return self::create($data);
    }

    /**
     * Accessor para nombre completo del afiliado
     */
    public function getNombreCompletoAttribute()
    {
        if ($this->afi_apellido && $this->afi_nombre) {
            return $this->afi_apellido . ', ' . $this->afi_nombre;
        }
        return null;
    }

    /**
     * Accessor para badge de status
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'OK' => 'success',
            'NO' => 'danger',
            'PEND' => 'warning',
            'ERROR' => 'danger',
        ];
        $color = $colors[$this->status] ?? 'default';
        return "<span class='badge badge-{$color}'>{$this->status}</span>";
    }

    /**
     * Accessor para tipo de anulación en texto
     */
    public function getTipoAnulacionTextoAttribute()
    {
        $tipos = [
            'IDTRAN' => 'Por ID de Transacción',
            'MSGID' => 'Por ID de Mensaje',
            'IDAUT' => 'Por ID de Autorización',
        ];
        return $tipos[$this->tipoidanul] ?? $this->tipoidanul;
    }
}
