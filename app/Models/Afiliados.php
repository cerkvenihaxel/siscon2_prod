<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliados extends Model
{
    use HasFactory;
    protected $table = 'afiliados';

    public function afiliadonumber(){
        return $this->hasMany(AfiliadosArticulos::class, 'nro_afiliado');
    }

    public function pedidosMedicamento()
    {
        return $this->hasMany(PedidoMedicamento::class, 'afiliados_id', 'id');
    }

    public function oficinaAutorizar(){
        return $this->hasMany(PedidoMedicamento::class, 'afiliados', 'id');
    }
}
