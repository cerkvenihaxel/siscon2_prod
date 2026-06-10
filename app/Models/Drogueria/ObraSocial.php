<?php

namespace App\Models\Drogueria;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de obras sociales (conexión droguería).
 *
 * Permite escalar el flujo a nuevas obras sociales sin tocar código: basta con
 * agregar una fila acá y etiquetar los consumos con su os_id.
 */
class ObraSocial extends Model
{
    protected $connection = 'drogueria';
    protected $table = 'obras_sociales';

    protected $fillable = ['codigo', 'nombre', 'convenios', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function consumos()
    {
        return $this->hasMany(ObraSocialConsumo::class, 'os_id');
    }

    /**
     * Resuelve una obra social por su código corto (ej: OSPLAD).
     */
    public static function porCodigo(string $codigo): ?self
    {
        return static::where('codigo', $codigo)->where('activo', true)->first();
    }
}
