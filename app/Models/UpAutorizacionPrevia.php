<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpAutorizacionPrevia extends Model
{
    protected $table = 'up_autorizacion_previa';

    protected $fillable = [
        'codigo_afiliado',
        'plan',
        'vercred',
        'token',
        'verifid',
        'prestador_id',
        'prestador_nombre',
        'usrid',
        'usrpass',
        'contexto_tipo',
        'fecha_autorizacion',
        'msgid',
        'idtran',
        'idaut',
        'status',
        'response_code',
        'response_message',
        'afi_codigo',
        'afi_apellido',
        'afi_nombre',
        'afi_plan',
        'afi_plan_nombre',
        'prestaciones',
        'prestaciones_respuesta',
        'importe_total',
        'importe_os',
        'importe_afiliado',
        'response_data',
        'execution_time_ms',
        'usuario_creador',
        'observaciones',
    ];

    protected $casts = [
        'fecha_autorizacion' => 'date',
        'prestaciones' => 'array',
        'prestaciones_respuesta' => 'array',
        'response_data' => 'array',
        'importe_total' => 'decimal:2',
        'importe_os' => 'decimal:2',
        'importe_afiliado' => 'decimal:2',
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
            'verifid' => $params['verifid'] ?? 'MANUAL',
            'prestador_id' => $params['prestador_id'] ?? config('union_personal.prestador_id'),
            'prestador_nombre' => $params['prestador_nombre'] ?? null,
            'usrid' => $params['usrid'] ?? config('union_personal.user_id'),
            'usrpass' => $params['usrpass'] ?? config('union_personal.user_pass'),
            'contexto_tipo' => $params['contexto_tipo'] ?? 'A',
            'fecha_autorizacion' => $params['fecha'] ?? now()->format('Y-m-d'),
            'msgid' => $params['msgid'] ?? null,
            'idtran' => $resultado['idtran'] ?? null,
            'idaut' => $resultado['idaut'] ?? null,
            'status' => $resultado['success'] ? ($resultado['data']['STATUS'] ?? 'ERROR') : 'ERROR',
            'response_code' => $resultado['data']['RSPCODG'] ?? null,
            'response_message' => $resultado['message'] ?? null,
            'prestaciones' => $params['prestaciones'] ?? null,
            'prestaciones_respuesta' => $resultado['data']['PR'] ?? null,
            'response_data' => $resultado['data'] ?? null,
            'execution_time_ms' => $executionTime,
            'usuario_creador' => \CRUDBooster::myEmail(),
        ];

        // Datos del afiliado
        if (isset($resultado['data']['AFICODIGO'])) {
            $data['afi_codigo'] = $resultado['data']['AFICODIGO'];
            $data['afi_apellido'] = $resultado['data']['AFIAPE'] ?? null;
            $data['afi_nombre'] = $resultado['data']['AFINOM'] ?? null;
            $data['afi_plan'] = $resultado['data']['AFIPLAN'] ?? null;
            $data['afi_plan_nombre'] = $resultado['data']['AFIPLANNOM'] ?? null;
        }

        // Calcular importes totales
        if (isset($resultado['data']['PR']) && is_array($resultado['data']['PR'])) {
            $importeTotal = 0;
            $importeOs = 0;
            $importeAfiliado = 0;

            foreach ($resultado['data']['PR'] as $pr) {
                $importeTotal += (float)($pr['IMPTOTAL'] ?? 0);
                $importeOs += (float)($pr['IMPOS'] ?? 0);
                $importeAfiliado += (float)($pr['CARGO'] ?? 0);
            }

            $data['importe_total'] = $importeTotal;
            $data['importe_os'] = $importeOs;
            $data['importe_afiliado'] = $importeAfiliado;
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
}
