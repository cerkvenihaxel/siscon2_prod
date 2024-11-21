<?php

namespace App\View\Components;

use App\Models\PuntoRetiro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

class ReporteMedicamentosParticularComponent extends Component
{
    protected $puntoRetiro;

    public function __construct()
    {
        // Realiza una consulta SQL para obtener los puntos de retiro
        $this->puntoRetiro = PuntoRetiro::all();
        Log::info($this->puntoRetiro);
    }

    public function render()
    {
        // Pasa la variable $puntoRetiro a la vista
        $puntoRetiro = $this->puntoRetiro;
        return view('components.reporte-medicamentos-particular-component', compact('puntoRetiro'));
    }
}
