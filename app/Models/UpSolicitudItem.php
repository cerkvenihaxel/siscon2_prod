<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpSolicitudItem extends Model
{
    protected $table = 'up_solicitud_items';

    protected $fillable = [
        'solicitud_id',
        'tipo_prestacion',
        'cod_prestacion',
        'descripcion',
        'cantidad',
        'troquel',
        'cod_barra',
        'cargo',
        'impos',
        'impot',
        'imptot',
        'adic',
        'estado_item',
        'rspcodp',
        'rspmsgp',
    ];

    protected $casts = [
        'cargo' => 'decimal:2',
        'impos' => 'decimal:2',
        'impot' => 'decimal:2',
        'imptot' => 'decimal:2',
        'adic' => 'decimal:2',
    ];

    /**
     * Relación con la solicitud
     */
    public function solicitud()
    {
        return $this->belongsTo(UpSolicitud::class, 'solicitud_id');
    }

    /**
     * Verificar si es medicamento
     */
    public function esMedicamento()
    {
        return $this->tipo_prestacion === 'M';
    }

    /**
     * Verificar si es prestación
     */
    public function esPrestacion()
    {
        return $this->tipo_prestacion === 'P';
    }

    /**
     * Verificar si es derivación
     */
    public function esDerivacion()
    {
        return $this->tipo_prestacion === 'D';
    }

    /**
     * Actualizar desde respuesta SOAP
     */
    public function actualizarDesdeRespuestaSOAP(array $prData)
    {
        $this->update([
            'descripcion' => $prData['DESCRIPCION'] ?? $this->descripcion,
            'cargo' => $prData['CARGO'] ?? null,
            'impos' => $prData['IMPOS'] ?? null,
            'impot' => $prData['IMPOT'] ?? null,
            'imptot' => $prData['IMPTOTAL'] ?? null,
            'adic' => $prData['ADIC'] ?? null,
            'estado_item' => $prData['STATUS'] ?? 'pendiente',
            'rspcodp' => $prData['RSPCODP'] ?? null,
            'rspmsgp' => $prData['RSPMSGP'] ?? null,
        ]);
    }
}
