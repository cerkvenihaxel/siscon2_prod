<?php

// Ejemplo de uso del servicio TwilioSender en el flujo de transacciones AP

use App\Services\TwilioSender;

class EjemploUsoTwilioSender
{
    private $twilioSender;

    public function __construct()
    {
        $this->twilioSender = new TwilioSender();
    }

    // Ejemplo 1: Enviar notificación cuando se registra el consumo (notification-approved)
    public function enviarNotificacionAprobada($xmlResponse)
    {
        // Extraer datos del XML
        $idAfiliado = $this->extraerIdAfiliadoDelXML($xmlResponse);
        $nombreAfiliado = $this->extraerNombreAfiliadoDelXML($xmlResponse);
        $fechaProceso = date('d/m/Y');

        // Enviar notificación
        $resultado = $this->twilioSender->sendNotificationApproved(
            $idAfiliado,
            $nombreAfiliado,
            $fechaProceso
        );

        if ($resultado) {
            \Log::info("Notificación de aprobación enviada para afiliado: {$idAfiliado}");
        } else {
            \Log::error("Error enviando notificación de aprobación para afiliado: {$idAfiliado}");
        }

        return $resultado;
    }

    // Ejemplo 2: Enviar mensaje general cuando está en tránsito (general-message)
    public function enviarMensajeTransito($xmlResponse)
    {
        // Extraer datos del XML
        $idAfiliado = $this->extraerIdAfiliadoDelXML($xmlResponse);
        $nombreAfiliado = $this->extraerNombreAfiliadoDelXML($xmlResponse);
        $mensaje = "su medicación está en tránsito y será entregada pronto.";

        // Enviar mensaje general
        $resultado = $this->twilioSender->sendGeneralMessage(
            $idAfiliado,
            $nombreAfiliado,
            $mensaje
        );

        if ($resultado) {
            \Log::info("Mensaje de tránsito enviado para afiliado: {$idAfiliado}");
        } else {
            \Log::error("Error enviando mensaje de tránsito para afiliado: {$idAfiliado}");
        }

        return $resultado;
    }

    // Métodos auxiliares para extraer datos del XML
    private function extraerIdAfiliadoDelXML($xmlResponse)
    {
        // Implementar lógica para extraer ID del afiliado del XML
        // Ejemplo: return $xmlResponse->afiliado->id;
        return '54715500'; // Ejemplo
    }

    private function extraerNombreAfiliadoDelXML($xmlResponse)
    {
        // Implementar lógica para extraer nombre del afiliado del XML
        // Ejemplo: return $xmlResponse->afiliado->nombre;
        return 'AXEL CERKVENIH'; // Ejemplo
    }
}
