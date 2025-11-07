<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpUsuarioDatos extends Model
{
    use HasFactory;

    protected $table = 'up_usuarios_datos';

    protected $fillable = [
        'id_afiliado',
        'telefono',
        'email'
    ];
}
