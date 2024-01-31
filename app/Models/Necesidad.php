<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Necesidad extends Model
{
    use HasFactory;
    protected $table = 'necesidad';
    public function entrantes()
    {
        return $this->hasMany(Entrante::class, 'necesidad', 'id');
    }
}
