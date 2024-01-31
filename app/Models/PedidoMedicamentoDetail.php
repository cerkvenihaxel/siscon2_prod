<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoMedicamentoDetail extends Model
{
    use HasFactory;
    protected $table = 'pedido_medicamento_detail';

    public function pedidoMedicamento()
    {
        return $this->belongsTo(PedidoMedicamento::class, 'pedido_medicamento_id');
    }
}
