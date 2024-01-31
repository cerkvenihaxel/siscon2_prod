<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    public function entrantes()
    {
        return $this->hasMany(Entrante::class, 'medicos_id');
    }
}
