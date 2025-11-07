<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwilioSenderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_afiliado',
        'telefono',
        'endpoint',
        'message_vars',
        'exitoso',
        'respuesta',
        'error'
    ];

    protected $casts = [
        'message_vars' => 'array',
        'exitoso' => 'boolean'
    ];
}
