<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoC;

class LinPedido extends Model
{
    use HasFactory;
    protected $table = 'lin_pedido';

    public function pedido()
    {
        return $this->belongsTo(PedidoC::class, 'id_pedido');
    }
}
