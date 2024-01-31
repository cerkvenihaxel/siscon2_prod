<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadosArticulos extends Model
{
    use HasFactory;
    protected $table = 'afiliados_articulos';

    public function afiliadonumber(){
        return $this->belongsTo(Afiliados::class, 'nro_afiliado');
    }

    public function nroArticulo(){
        return $this->belongsTo(ArticulosZafiro::class, 'id_articulo');
    }

    public function patologias(){
        return $this->belongsTo(Patologias::class, 'id');
    }

}
