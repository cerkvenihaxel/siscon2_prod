<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquiposOxigenoterapia extends Model
{
    use HasFactory;

    protected $table = 'equipos_oxigenoterapia';

    protected $fillable = [
        'codigo_equipo',
        'nombre_equipo',
        'nombre_completo',
        'marca',
        'modelo',
        'nro_serie',
        'tipo_equipo',
        'descripcion',
        'estado_equipo',
        'fecha_adquisicion',
        'fecha_ultimo_mantenimiento',
        'fecha_proximo_mantenimiento',
        'observaciones',
        'stamp_user'
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'fecha_ultimo_mantenimiento' => 'date',
        'fecha_proximo_mantenimiento' => 'date',
    ];

    public function prestamos()
    {
        return $this->hasMany(PrestamoOxigenoterapia::class, 'nro_serie_equipo', 'nro_serie');
    }

    public function prestamoActivo()
    {
        return $this->hasOne(PrestamoOxigenoterapia::class, 'nro_serie_equipo', 'nro_serie')
                    ->where('estado_prestamo', 'ACTIVO');
    }

    public function isDisponible()
    {
        return $this->estado_equipo === 'DISPONIBLE';
    }

    public function isEnUso()
    {
        return $this->estado_equipo === 'EN_USO';
    }

    public function isMantenimiento()
    {
        return $this->estado_equipo === 'MANTENIMIENTO';
    }

    public function isRetirado()
    {
        return $this->estado_equipo === 'RETIRADO';
    }

    /**
     * Obtiene el nombre completo del equipo con marca y modelo
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre_equipo} - {$this->marca} {$this->modelo}";
    }

    /**
     * Obtiene el nombre completo del equipo con código
     */
    public function getNombreConCodigoAttribute()
    {
        return "{$this->codigo_equipo} - {$this->nombre_equipo} - {$this->marca} {$this->modelo}";
    }
} 