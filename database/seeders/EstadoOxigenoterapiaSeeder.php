<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoOxigenoterapiaSeeder extends Seeder
{
    public function run()
    {
        $estados = [
            [
                'estado' => 'PENDIENTE',
                'descripcion' => 'Solicitud pendiente de autorización',
                'color' => 'warning',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado' => 'AUTORIZADO',
                'descripcion' => 'Solicitud autorizada, pendiente de préstamo',
                'color' => 'info',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado' => 'EN_PRESTAMO',
                'descripcion' => 'Equipo en préstamo activo',
                'color' => 'success',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado' => 'RENOVADO',
                'descripcion' => 'Préstamo renovado',
                'color' => 'primary',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado' => 'FINALIZADO',
                'descripcion' => 'Préstamo finalizado',
                'color' => 'default',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado' => 'RECHAZADO',
                'descripcion' => 'Solicitud rechazada',
                'color' => 'danger',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('estado_oxigenoterapia')->insert($estados);
    }
} 