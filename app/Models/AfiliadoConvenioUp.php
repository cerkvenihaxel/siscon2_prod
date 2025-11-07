<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfiliadoConvenioUp extends Model
{
    protected $table = 'afiliados_convenio_up';

    protected $fillable = [
        'codigo_afiliado',
        'apellido',
        'nombre',
        'plan',
        'plan_nombre',
        'sexo',
        'fecha_nacimiento',
        'edad',
        'codigo_postal',
        'localidad',
        'provincia',
        'tipo_afiliado',
        'titofam',
        'ultima_version_credencial',
        'ultima_verificacion_soap',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'ultima_verificacion_soap' => 'datetime',
    ];

    /**
     * Relación con solicitudes
     */
    public function solicitudes()
    {
        return $this->hasMany(UpSolicitud::class, 'codigo_afiliado', 'codigo_afiliado');
    }

    /**
     * Relación con transacciones SOAP
     */
    public function transacciones()
    {
        return $this->hasMany(UpTransaccionSoap::class, 'afiliado_codigo', 'codigo_afiliado');
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->apellido . ', ' . $this->nombre);
    }

    /**
     * Crear o actualizar afiliado desde datos SOAP
     */
    public static function createOrUpdateFromSoap(array $soapData)
    {
        if (empty($soapData['AFICODIGO'])) {
            return null;
        }

        return self::updateOrCreate(
            ['codigo_afiliado' => $soapData['AFICODIGO']],
            [
                'apellido' => $soapData['AFIAPE'] ?? null,
                'nombre' => $soapData['AFINOM'] ?? null,
                'plan' => $soapData['AFIPLAN'] ?? null,
                'plan_nombre' => $soapData['AFIPLANNOM'] ?? null,
                'sexo' => $soapData['AFISEXO'] ?? null,
                'fecha_nacimiento' => isset($soapData['AFIFECNAC']) ? self::parseFechaSoap($soapData['AFIFECNAC']) : null,
                'codigo_postal' => $soapData['AFICP'] ?? null,
                'localidad' => $soapData['AFILOC'] ?? null,
                'provincia' => $soapData['AFIPROV'] ?? null,
                'tipo_afiliado' => $soapData['AFITIPO'] ?? null,
                'titofam' => $soapData['AFITITOFAM'] ?? null,
                'ultima_version_credencial' => $soapData['AFIVERCRED'] ?? null,
                'ultima_verificacion_soap' => now(),
            ]
        );
    }

    /**
     * Parsear fecha desde formato SOAP (DD/MM/YYYY)
     */
    private static function parseFechaSoap($fecha)
    {
        try {
            if (empty($fecha)) {
                return null;
            }

            // Si viene en formato DD/MM/YYYY
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $matches)) {
                return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }

            return $fecha;
        } catch (\Exception $e) {
            return null;
        }
    }
}
