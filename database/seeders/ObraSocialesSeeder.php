<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObraSocialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('obras_sociales')->insert([
            [
            'created_at' => now(),
            'updated_at' => now(),
            'nombre'=>'Incluir Salud',
            'codigo_social' => rand(10, 500),
            'direccion' => ' ',
            'telefono' => ' ',
            'email' => ' ',
            'web' => ' ',
            'provincia' => 'San Juan',
            'localidad' => 'San Juan',
            ],

            [
            'created_at' => now(),
            'updated_at' => now(),
            'nombre'=>'Ministerio de Salud',
            'codigo_social' => rand(10, 500),
            'direccion' => ' ',
            'telefono' => ' ',
            'email' => ' ',
            'web' => ' ',
            'provincia' => 'La Rioja',
            'localidad' => 'La Rioja',
            ],
            [
            'created_at' => now(),
            'updated_at' => now(),
            'nombre'=>'APOS',
            'codigo_social' => rand(10, 500),
            'direccion' => ' ',
            'telefono' => ' ',
            'email' => ' ',
            'web' => ' ',
            'provincia' => 'La Rioja',
            'localidad' => 'La Rioja',
            ]

        ],



        );
    }
}
