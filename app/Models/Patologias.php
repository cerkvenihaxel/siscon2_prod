<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patologias extends Model
{
    use HasFactory;
    protected $table = 'patologias';

    public function patologias(){
        return $this->hasMany(AfiliadosArticulos::class, 'patologias');
    }

    public function oficinaAutorizar(){
        return $this->hasMany(OficinaAutorizar::class, 'patologia', 'id');
    }

    public function pedidoMedicamento(){
        return $this->hasMany(PedidoMedicamento::class, 'patologias', 'id');
    }
}
