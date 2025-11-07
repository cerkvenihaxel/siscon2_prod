<?php

namespace App\Observers;

use App\Models\UpConsumo;
use App\Services\TwilioSender;
use CRUDBooster;

class UpConsumoObserver
{
    private $twilioSender;

    public function __construct()
    {
        $this->twilioSender = new TwilioSender();
    }

    public function updated(UpConsumo $consumo)
    {
        // Verificar flag antes de proceder
        if (!env('SEND_NOTIFICATION_WSP_FLAG', false)) {
            return;
        }

        // Verificar si se agregó nro_transporte (pasó a en_transito)
        if ($consumo->isDirty('nro_transporte') && !empty($consumo->nro_transporte)) {
            $this->enviarNotificacionTransito($consumo);
        }
    }

    private function enviarNotificacionTransito(UpConsumo $consumo)
    {
        try {
            $nombreCompleto = trim($consumo->nombres . ' ' . $consumo->apellidos);
            $detallesConsumo = $consumo->desc ?? 'su medicación';
            $myName = CRUDBooster::myName() ?? 'Global Médica S.A.';
            
            $mensaje = "su medicación estará lista para ser retirada pronto: {$detallesConsumo}";
            
            $this->twilioSender->sendGeneralMessage(
                $consumo->afiliado,
                $nombreCompleto,
                $mensaje,
                $myName
            );
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de tránsito: ' . $e->getMessage());
        }
    }
}
