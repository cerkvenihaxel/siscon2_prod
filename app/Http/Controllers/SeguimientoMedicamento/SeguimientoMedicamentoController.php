<?php

namespace App\Http\Controllers\SeguimientoMedicamento;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeguimientoMedicamento\SeguimientoMedicamentoRequest;
use App\Services\SeguimientoMedicamentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeguimientoMedicamentoController extends Controller
{
    protected $seguimientoMedicamentoService;

    public function __construct(SeguimientoMedicamentoService $seguimientoMedicamentoService)
    {
        $this->seguimientoMedicamentoService = $seguimientoMedicamentoService;
    }
    public function index(SeguimientoMedicamentoRequest $request): ?JsonResponse
    {
        return $this->seguimientoMedicamentoService->timeLine($request);
    }
}
