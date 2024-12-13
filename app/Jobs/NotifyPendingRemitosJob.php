<?php

namespace App\Jobs;

use App\Models\PuntoRetiro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\CotizacionConvenio;
use Illuminate\Support\Facades\Log;

class NotifyPendingRemitosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Obtener el mes y año actuales
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Obtener las últimas 10 filas del mes corriente que cumplen con la condición
        $pendientes = CotizacionConvenio::whereNotNull('nro_remito')
            ->where('notificated', false)
            ->whereMonth('created_at', $currentMonth) // Filtro por mes
            ->whereYear('created_at', $currentYear) // Filtro por año
            ->orderBy('created_at', 'desc') // Ordenar por la fecha de creación más reciente
            ->limit(1) // Limitar a los últimos 10 registros
            ->get();

        foreach ($pendientes as $item) {
            $nombre = $item->nombreyapellido;
            $punto_retiro = $item->punto_retiro_id;
            $punto_retiro = PuntoRetiro::where('id', $punto_retiro)->first();
            $telefono = $item->tel_afiliado;

            // Formatear los datos para el POST
            $data = [
                'phone' => $telefono,
                'message_vars' => json_encode([
                    '1' => $nombre,
                    '2' => $punto_retiro->nombre,
                    '3' => $punto_retiro->horario,
                    '4' => $punto_retiro->telefono,
                    '5' => $punto_retiro->direccion
                ])
            ];

            // Realizar el POST
            $response = Http::post('http://localhost:8080/notification-medication', $data);

            if ($response->successful()) {
                // Marcar como notificado
                $item->update(['notificated' => true]);
            }
        }
    }
}
