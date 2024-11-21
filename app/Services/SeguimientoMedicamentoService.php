<?php

namespace App\Services;

use App\Http\Requests\SeguimientoMedicamento\SeguimientoMedicamentoRequest;
use App\Models\Afiliados;
use App\Models\CotizacionConvenio;
use App\Models\PedidoMedicamento;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class SeguimientoMedicamentoService
{
    public function timeLine(SeguimientoMedicamentoRequest $request): ?JsonResponse
    {
        $nro_solicitud = $request->input('nro_solicitud');

        // Obtenemos los datos necesarios
        $userData = $this->userData($nro_solicitud);
        $incoming = $this->incoming($nro_solicitud);
        $processed = $this->processed($nro_solicitud);
        $readyToPick = $this->readyToPick($nro_solicitud);
        $delivered = $this->delivered($nro_solicitud);

        // Construimos el JSON estructurado
        $timeline = [
            'userData' => [
              'name' => $userData['nombreAfiliado'],
                'affiliate_number' => $userData['nroAfiliado']
            ],
            'incoming' => [
                'status' => $incoming['status'],
                'created_at' => $incoming['created_at'] ?? null,
                'message' => $incoming['status'] ? 'Su pedido fue ingresado.' : 'Pedido no encontrado.',
            ],
            'processed' => [
                'status' => $processed['status'],
                'created_at' => $processed['created_at'] ?? null,
                'message' => $processed['status'] ? 'Su pedido está siendo procesado.' : 'El pedido no está en procesamiento.',
            ],
            'readyToPick' => [
                'status' => $readyToPick['status'],
                'created_at' => $readyToPick['created_at'] ?? null,
                'message' => $readyToPick['status'] ? 'El pedido está listo para ser retirado.' : 'El pedido no está listo para retiro.',
            ],
            'delivered' => [
                'status' => $delivered['status'],
                'updated_at' => $delivered['updated_at'] ?? null,
                'message' => $delivered['status'] ? 'El pedido ha sido entregado.' : 'El pedido no ha sido entregado.',
            ],
        ];

        return response()->json($timeline);
    }

    private function incoming($nro_solicitud)
    {
        $pedido = PedidoMedicamento::where('nrosolicitud', $nro_solicitud)->first();
        return [
            'status' => !empty($pedido),
            'created_at' => $pedido->created_at ?? null,
        ];
    }

    private function processed($nro_solicitud)
    {
        $processed = CotizacionConvenio::where('nrosolicitud', $nro_solicitud)->first();
        return [
            'status' => !empty($processed),
            'created_at' => $processed->created_at ?? null,
        ];
    }

    private function readyToPick($nro_solicitud)
    {
        $readyToPick = CotizacionConvenio::where('nrosolicitud', $nro_solicitud)
            ->whereNotNull('nro_remito')
            ->first();

        return [
            'status' => !empty($readyToPick),
            'created_at' => $readyToPick->updated_at ?? null,
        ];
    }

    private function delivered($nro_solicitud)
    {
        $delivered = CotizacionConvenio::where('nrosolicitud', $nro_solicitud)
            ->where('estado_solicitud_id', 13)
            ->first();

        return [
            'status' => !empty($delivered),
            'updated_at' => $delivered->updated_at ?? null,
        ];
    }

    private function userData($nro_solicitud)
    {
        $userData = PedidoMedicamento::where('nrosolicitud', $nro_solicitud)->first();
        $userName = Afiliados::where('nroAfiliado', $userData->nroAfiliado)->value('apeynombres');

        return [
            'nroAfiliado' => $userData->nroAfiliado,
            'nombreAfiliado' => $userName
        ];
    }
}
