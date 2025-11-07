<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpElegibilidad extends Model
{
    protected $table = 'up_elegibilidad';

    protected $fillable = [
        'codigo_afiliado',
        'plan',
        'vercred',
        'token',
        'verifid',
        'prestador_id',
        'usrid',
        'usrpass',
        'msgid',
        'idtran',
        'status',
        'response_code',
        'response_message',
        'afi_codigo',
        'afi_apellido',
        'afi_nombre',
        'afi_plan',
        'afi_plan_nombre',
        'afi_sexo',
        'afi_fecha_nacimiento',
        'afi_codigo_postal',
        'afi_localidad',
        'afi_provincia',
        'afi_vercred',
        'prestaciones',
        'response_data',
        'execution_time_ms',
        'usuario_creador',
        'observaciones',
    ];

    protected $casts = [
        'prestaciones' => 'array',
        'response_data' => 'array',
        'afi_fecha_nacimiento' => 'date',
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
            'usrid' => $params['usrid'] ?? config('union_personal.user_id'),
            'usrpass' => $params['usrpass'] ?? config('union_personal.user_pass'),
            'msgid' => $params['msgid'] ?? null,
            'idtran' => $resultado['idtran'] ?? null,
            'status' => $resultado['success'] ? ($resultado['data']['STATUS'] ?? 'ERROR') : 'ERROR',
            'response_code' => $resultado['data']['RSPCODG'] ?? null,
            'response_message' => $resultado['message'] ?? null,
            'prestaciones' => $params['prestaciones'] ?? null,
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
            $data['afi_sexo'] = $resultado['data']['AFISEXO'] ?? null;
            $data['afi_codigo_postal'] = $resultado['data']['AFICP'] ?? null;
            $data['afi_localidad'] = $resultado['data']['AFILOC'] ?? null;
            $data['afi_provincia'] = $resultado['data']['AFIPROV'] ?? null;
            $data['afi_vercred'] = $resultado['data']['AFIVERCRED'] ?? null;

            // Parsear fecha de nacimiento
            if (!empty($resultado['data']['AFIFECNAC'])) {
                try {
                    $data['afi_fecha_nacimiento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $resultado['data']['AFIFECNAC'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $data['afi_fecha_nacimiento'] = null;
                }
            }
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
