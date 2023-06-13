<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadosArticulosModel extends Model
{
    protected $table = 'afiliados_articulos';
    protected $fillable = ['nro_afiliado', 'id_articulo', 'cantidad', 'patologias', 'des_articulo', 'presentacion'];
}
