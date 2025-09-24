<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Afiliados extends Model
{
    use HasFactory;
    protected $table = 'afiliados';

    protected $fillable = [
        'nroAfiliado',
        'documento',
        'apeynombres',
        'localidad',
        'telefonos',
        'email',
        'sexo',
        'obra_social',
        'fecha_nacimiento',
        'zona_residencia',
        'domicilio',
        'obra_social_id'
    ];

    protected $dates = [
        'fecha_nacimiento'
    ];

    public function afiliadonumber(){
        return $this->hasMany(AfiliadosArticulos::class, 'nro_afiliado');
    }

    public function pedidosMedicamento()
    {
        return $this->hasMany(PedidoMedicamento::class, 'afiliados_id', 'id');
    }

    public function oficinaAutorizar(){
        return $this->hasMany(PedidoMedicamento::class, 'afiliados_id', 'id');
    }

    /**
     * Obtener la edad calculada del afiliado
     */
    public function getEdadAttribute()
    {
        if ($this->fecha_nacimiento) {
            return Carbon::parse($this->fecha_nacimiento)->age;
        }
        return null;
    }

    /**
     * Obtener el nombre completo formateado
     */
    public function getNombreCompletoAttribute()
    {
        return $this->apeynombres ?? '';
    }

    /**
     * Obtener el teléfono formateado
     */
    public function getTelefonoFormateadoAttribute()
    {
        return $this->telefonos ?? '';
    }

    /**
     * Obtener la zona de residencia con valor por defecto
     */
    public function getZonaResidenciaAttribute($value)
    {
        return $value ?? 'Centro';
    }

    /**
     * Scope para buscar por número de afiliado
     */
    public function scopePorNumeroAfiliado($query, $nroAfiliado)
    {
        return $query->where('nroAfiliado', $nroAfiliado);
    }

    /**
     * Scope para buscar por documento
     */
    public function scopePorDocumento($query, $documento)
    {
        return $query->where('documento', $documento);
    }

    /**
     * Obtener datos completos del afiliado para el formulario
     */
    public function getDatosCompletos()
    {
        return [
            'nroAfiliado' => $this->nroAfiliado,
            'apeynombres' => $this->apeynombres,
            'documento' => $this->documento,
            'edad' => $this->edad,
            'zona_residencia' => $this->zona_residencia,
            'telefonos' => $this->telefonos,
            'email' => $this->email,
            'sexo' => $this->sexo,
            'localidad' => $this->localidad
        ];
    }
}
