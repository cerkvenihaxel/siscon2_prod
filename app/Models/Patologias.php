<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patologias extends Model
{
    use HasFactory;
    protected $table = 'patologias';

    public function patologias(){
        return $this->hasMany(AfiliadosArticulos::class, 'patologias');
    }
}
