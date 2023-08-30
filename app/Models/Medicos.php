<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicos extends Model
{
    use HasFactory;
    protected $table = 'medicos';

    public function oficinaAutorizar()
    {
        return $this->hasMany(OficinaAutorizar::class, 'medicos_id', 'id');
    }
}
