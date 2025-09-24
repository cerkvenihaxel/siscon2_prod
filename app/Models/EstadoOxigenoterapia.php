<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoOxigenoterapia extends Model
{
    use HasFactory;

    protected $table = 'estado_oxigenoterapia';

    protected $fillable = [
        'estado',
        'descripcion',
        'color',
        'activo'
    ];

    public function pedidosOxigenoterapia()
    {
        return $this->hasMany(PedidoOxigenoterapia::class, 'estado_oxigenoterapia_id');
    }
} 