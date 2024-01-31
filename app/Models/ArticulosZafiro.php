<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticulosZafiro extends Model
{
    use HasFactory;
    protected $table = 'articulosZafiro';

    public function nroArticulo(){
        return $this->hasMany(AfiliadosArticulos::class, 'id_articulo');
    }
}
