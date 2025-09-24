<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquiposOxigenoterapiaSeeder extends Seeder
{
    public function run()
    {
        $equipos = [
            [
                'codigo_equipo' => 'OXI-001',
                'nombre_equipo' => 'Concentrador de Oxígeno Portátil',
                'marca' => 'Philips',
                'modelo' => 'SimplyGo',
                'nro_serie' => 'PHIL-001-2024',
                'tipo_equipo' => 'CONCENTRADOR',
                'descripcion' => 'Concentrador de oxígeno portátil de 5L/min',
                'estado_equipo' => 'DISPONIBLE',
                'fecha_adquisicion' => '2024-01-01',
                'stamp_user' => 'admin@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_equipo' => 'OXI-002',
                'nombre_equipo' => 'Concentrador de Oxígeno Fijo',
                'marca' => 'Invacare',
                'modelo' => 'Perfecto2',
                'nro_serie' => 'INV-002-2024',
                'tipo_equipo' => 'CONCENTRADOR',
                'descripcion' => 'Concentrador de oxígeno fijo de 5L/min',
                'estado_equipo' => 'DISPONIBLE',
                'fecha_adquisicion' => '2024-01-01',
                'stamp_user' => 'admin@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_equipo' => 'OXI-003',
                'nombre_equipo' => 'Tanque de Oxígeno',
                'marca' => 'Air Liquide',
                'modelo' => 'AL-10L',
                'nro_serie' => 'AIR-003-2024',
                'tipo_equipo' => 'TANQUE',
                'descripcion' => 'Tanque de oxígeno de 10 litros',
                'estado_equipo' => 'DISPONIBLE',
                'fecha_adquisicion' => '2024-01-01',
                'stamp_user' => 'admin@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_equipo' => 'OXI-004',
                'nombre_equipo' => 'Mascarilla de Oxígeno',
                'marca' => 'Salter Labs',
                'modelo' => '1600',
                'nro_serie' => 'SAL-004-2024',
                'tipo_equipo' => 'MASCARILLA',
                'descripcion' => 'Mascarilla de oxígeno con cánula nasal',
                'estado_equipo' => 'DISPONIBLE',
                'fecha_adquisicion' => '2024-01-01',
                'stamp_user' => 'admin@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_equipo' => 'OXI-005',
                'nombre_equipo' => 'Concentrador de Oxígeno Portátil',
                'marca' => 'Inogen',
                'modelo' => 'One G3',
                'nro_serie' => 'INO-005-2024',
                'tipo_equipo' => 'CONCENTRADOR',
                'descripcion' => 'Concentrador de oxígeno portátil de 3L/min',
                'estado_equipo' => 'DISPONIBLE',
                'fecha_adquisicion' => '2024-01-01',
                'stamp_user' => 'admin@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('equipos_oxigenoterapia')->insert($equipos);
    }
} 