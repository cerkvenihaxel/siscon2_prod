<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinica extends Model
{
    use HasFactory;

    public function entrantes()
    {
        return $this->hasMany(Entrante::class, 'clinicas_id');
    }
}
