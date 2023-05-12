<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrante extends Model
{
    use HasFactory;

    public function clinicas()
    {
        return $this->belongsTo(Clinica::class, 'clinicas_id');
    }

    public function medicos()
    {
        return $this->belongsTo(Medico::class, 'medicos_id');
    }

    public function estado_paciente()
    {
        return $this->belongsTo(EstadoPaciente::class, 'estado_paciente_id');
    }

    public function necesidad()
    {

        return $this->belongsTo(Necesidad::class, 'necesidad');
    }
}
