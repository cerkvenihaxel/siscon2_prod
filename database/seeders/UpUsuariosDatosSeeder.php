<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UpUsuarioDatos;

class UpUsuariosDatosSeeder extends Seeder
{
    public function run()
    {
        UpUsuarioDatos::create([
            'id_afiliado' => '54715500',
            'telefono' => '+5492612780455',
            'email' => 'axelcrkv@gmail.com'
        ]);
    }
}
