<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoC extends Model
{
    use HasFactory;
    protected $table = 'pedidos';

    public function linPedido()
    {
        return $this->hasMany(LinPedido::class, 'id_pedido');
    }
}
