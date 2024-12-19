<?php

namespace App\Jobs;

use App\Models\Afiliados;
use App\Models\PedidoMedicamento;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyApprovedMedication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pedidoMedicamentoId;

    public function __construct(int $pedidoMedicamentoId)
    {
        $this->pedidoMedicamentoId = $pedidoMedicamentoId;
    }

    public function handle()
    {
        $pedidoMedicamento = PedidoMedicamento::find($this->pedidoMedicamentoId);

        if(!$pedidoMedicamento){
          Log::error("Pedido medicamento inexistente");
          return;
        }

        $nombreAfiliado = Afiliados::where('id', $pedidoMedicamento->afiliados_id)->value('apeynombres');
        if (!$nombreAfiliado) {
            Log::error("Nombre del afiliado no encontrado para el pedido {$this->pedidoMedicamentoId}");
            return;
        }
        $fecha = Carbon::now()->format('d/m/Y H:i');
        $telefono = $pedidoMedicamento->tel_afiliado;

        $data = [
            'phone' => $telefono,
            'message_vars' => json_encode([
                '1' => $nombreAfiliado,
                '2' => $fecha
            ])
        ];

        $response = Http::post('http://127.0.0.1:8081/notification-approved', $data);

        if($response->successful())
        {
            $pedidoMedicamento->update([
                'notificated' => 1
            ]);
        }

    }
}
