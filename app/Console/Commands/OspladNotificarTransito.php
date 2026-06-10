<?php

namespace App\Console\Commands;

use App\Models\Drogueria\ObraSocialConsumo;
use App\Services\TwilioSender;
use Illuminate\Console\Command;

/**
 * Envía la notificación "procesando" (mensaje #1) a los afiliados cuyos consumos
 * pasaron a En tránsito (EM) y aún no fueron avisados.
 *
 * Idempotente vía la marca notif_transito_at. Pensado para correr de forma
 * programada (ver App\Console\Kernel) ya que el cambio de estado lo hace un
 * proceso externo (Zafiro/droguería), sin intervención de la farmacia.
 */
class OspladNotificarTransito extends Command
{
    protected $signature = 'osplad:notificar-transito {--limit=200 : Máximo de notificaciones por corrida}';
    protected $description = 'Notifica por WhatsApp a los afiliados con pedidos que pasaron a En tránsito (procesando)';

    public function handle(TwilioSender $twilio): int
    {
        $limit = (int) $this->option('limit');

        $pendientes = ObraSocialConsumo::sinNotificarTransito()
            ->orderBy('id_consumo')
            ->limit($limit)
            ->get();

        if ($pendientes->isEmpty()) {
            $this->info('No hay consumos En tránsito pendientes de notificar.');
            return self::SUCCESS;
        }

        $ok = 0;
        foreach ($pendientes as $c) {
            try {
                $twilio->sendObraSocialProcesando($c->telefono, $c->id_consumo);
                $c->notif_transito_at = now();
                $c->save();
                $ok++;
            } catch (\Throwable $e) {
                $this->error("Consumo {$c->id_consumo}: " . $e->getMessage());
            }
        }

        $this->info("Notificaciones 'procesando' enviadas: {$ok}/{$pendientes->count()}");
        return self::SUCCESS;
    }
}
