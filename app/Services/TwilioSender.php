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
