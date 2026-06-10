<?php

namespace App\Services;

use App\Models\UpUsuarioDatos;
use App\Models\TwilioSenderLog;
use Illuminate\Support\Facades\Http;

class TwilioSender
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SEND_NOTIFICATION_WSP_URL');
    }

    public function sendNotificationApproved($idAfiliado, $nombreAfiliado, $fechaProceso)
    {
        if (!env('SEND_NOTIFICATION_WSP_FLAG', false)) {
            return false;
        }

        $usuario = UpUsuarioDatos::where('id_afiliado', $idAfiliado)->first();
        
        if (!$usuario) {
            $this->logError($idAfiliado, '', '/notification-approved', [], 'Usuario no encontrado');
            return false;
        }

        $messageVars = [
            "1" => $nombreAfiliado,
            "2" => $fechaProceso
        ];

        return $this->sendRequest('/notification-approved', $usuario->telefono, $messageVars, $idAfiliado);
    }

    public function sendGeneralMessage($idAfiliado, $nombreAfiliado, $mensaje, $empresa = 'Global Médica S.A.')
    {
        if (!env('SEND_NOTIFICATION_WSP_FLAG', false)) {
            return false;
        }

        $usuario = UpUsuarioDatos::where('id_afiliado', $idAfiliado)->first();
        
        if (!$usuario) {
            $this->logError($idAfiliado, '', '/general-message', [], 'Usuario no encontrado');
            return false;
        }

        $messageVars = [
            "1" => $empresa,
            "2" => $mensaje,
            "3" => $empresa
        ];

        return $this->sendRequest('/general-message', $usuario->telefono, $messageVars, $idAfiliado);
    }

    /* ======================================================================
     | Notificaciones de Obra Social (OSPLAD y futuras)
     |
     | El teléfono viene en la propia fila de consumo (osplad_consumos.telefono).
     | Se usan dos mensajes, ambos vía /general-message (texto libre en la var "2"):
     |   1) Procesando: al pasar a En tránsito (automático).
     |   2) Entregado : al marcar la entrega (incluye N° de pedido/remito y fecha).
     | ====================================================================== */

    /**
     * Notificación #1 (procesando): se dispara automáticamente al pasar a En tránsito.
     */
    public function sendObraSocialProcesando($telefono, $ref = null)
    {
        if (!env('SEND_NOTIFICATION_WSP_FLAG', false)) {
            return false;
        }
        if (empty($telefono)) {
            $this->logError($ref, '', '/general-message', [], 'Teléfono vacío');
            return false;
        }

        $empresa = 'Global Médica';
        $mensaje = 'su medicación está siendo procesada. '
                 . 'Podrá retirarla por la sucursal en 96 hs (4 días hábiles).';
        $mensaje = $this->limpiarVariableTwilio($mensaje);

        $messageVars = ["1" => $empresa, "2" => $mensaje, "3" => $empresa];

        return $this->sendRequest('/general-message', $this->normalizarTelefono($telefono), $messageVars, $ref);
    }

    /**
     * Notificación #2 (entregado): se dispara al marcar la entrega.
     * El template arma: "...Queremos informarle que {mensaje}...".
     *
     * @param string $pedido   N° de pedido/remito entregado.
     * @param string $fecha    Fecha de entrega (d/m/Y).
     * @param string $farmacia Nombre de la farmacia donde se entregó.
     */
    public function sendObraSocialEntregado($telefono, $pedido, $fecha, $farmacia = null, $ref = null)
    {
        if (!env('SEND_NOTIFICATION_WSP_FLAG', false)) {
            return false;
        }
        if (empty($telefono)) {
            $this->logError($ref, '', '/general-message', [], 'Teléfono vacío');
            return false;
        }

        $empresa = 'Global Médica';
        $mensaje = 'su pedido ' . $pedido . ' ha sido entregado el día ' . $fecha;
        if (!empty($farmacia)) {
            $mensaje .= ' en la farmacia ' . $farmacia;
        }
        $mensaje .= '. ¡Muchas gracias!';

        // Twilio (error 21656) NO admite saltos de línea, tabs ni 4+ espacios
        // en las variables de plantilla: normalizamos a una sola línea.
        $mensaje = $this->limpiarVariableTwilio($mensaje);

        $messageVars = ["1" => $empresa, "2" => $mensaje, "3" => $empresa];

        return $this->sendRequest('/general-message', $this->normalizarTelefono($telefono), $messageVars, $ref);
    }

    /**
     * Sanea un valor para usarlo como variable de plantilla de Twilio.
     * Quita saltos de línea/tabs y colapsa espacios múltiples (evita ApiError 21656).
     */
    private function limpiarVariableTwilio($texto)
    {
        $texto = str_replace(["\r\n", "\r", "\n", "\t"], ' ', (string) $texto);
        $texto = preg_replace('/\s{2,}/', ' ', $texto);
        return trim($texto);
    }

    /**
     * Normaliza el teléfono a formato E.164 argentino para WhatsApp.
     * Acepta valores como "03865-421428", "2615692833", "+549...".
     */
    private function normalizarTelefono($telefono)
    {
        $tel = preg_replace('/[^0-9+]/', '', (string) $telefono);
        if ($tel === '') {
            return $tel;
        }
        if (strpos($tel, '+') === 0) {
            return $tel;
        }
        // Quitar 0 inicial (código de área nacional) y 15 no se maneja acá de forma agresiva
        $tel = ltrim($tel, '0');
        return '+549' . $tel;
    }

    private function sendRequest($endpoint, $phone, $messageVars, $idAfiliado)
    {
        try {
            $url = rtrim($this->baseUrl, '/') . $endpoint;
            
            $response = Http::post($url, [
                'phone' => $phone,
                'message_vars' => json_encode($messageVars)
            ]);

            if ($response->successful()) {
                $this->logSuccess($idAfiliado, $phone, $endpoint, $messageVars, $response->body());
                return true;
            } else {
                $this->logError($idAfiliado, $phone, $endpoint, $messageVars, $response->body());
                return false;
            }
        } catch (\Exception $e) {
            $this->logError($idAfiliado, $phone, $endpoint, $messageVars, $e->getMessage());
            return false;
        }
    }

    private function logSuccess($idAfiliado, $telefono, $endpoint, $messageVars, $respuesta)
    {
        TwilioSenderLog::create([
            'id_afiliado' => $idAfiliado,
            'telefono' => $telefono,
            'endpoint' => $endpoint,
            'message_vars' => $messageVars,
            'exitoso' => true,
            'respuesta' => $respuesta
        ]);
    }

    private function logError($idAfiliado, $telefono, $endpoint, $messageVars, $error)
    {
        TwilioSenderLog::create([
            'id_afiliado' => $idAfiliado,
            'telefono' => $telefono,
            'endpoint' => $endpoint,
            'message_vars' => $messageVars,
            'exitoso' => false,
            'error' => $error
        ]);
    }
}
