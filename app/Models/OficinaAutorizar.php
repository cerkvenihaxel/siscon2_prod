<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficinaAutorizar extends Model
{
    use HasFactory;
    protected $table = 'convenio_oficina_os';

    public function oficinaAutorizarDetail()
    {
        return $this->hasMany(OficinaAutorizarDetail::class, 'convenio_oficina_os_id');
    }


}
