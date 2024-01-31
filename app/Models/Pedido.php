<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $table = 'lin_pedido';
    protected $fillable = ['id_pedido', 'item', 'cantidad', 'des_articulo', 'presentacion', 'pcio_vta_uni_siva', 'coeficiente_pcio_modificado', 'pcio_com_uni_siva'];

}
