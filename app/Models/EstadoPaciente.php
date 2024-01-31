<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPaciente extends Model
{
    use HasFactory;
    protected $table = 'estado_paciente';
    public function entrantes()
    {
        return $this->hasMany(Entrante::class, 'estado_paciente_id');
    }
}
