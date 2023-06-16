<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoMedicamento extends Model
{
    use HasFactory;

    protected $table = 'pedido_medicamento';

    public function detalles()
    {
        return $this->hasMany(PedidoMedicamentoDetail::class, 'pedido_medicamento_id');
    }
}
