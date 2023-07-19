<?php

namespace App\Http\Livewire;

use App\Models\Entrante;
use Illuminate\Support\Facades\DB;
use Livewire\Component;


class Entrantes extends Component
{
    public $count;
    public $search;

    public function render()
    {
        $this->count = DB::table('entrantes')->where('estado_solicitud_id', 1)->count();

        $entrantes = Entrante::with(['clinicas', 'medicos', 'estado_paciente', 'nec'])
            ->where('estado_solicitud_id', 1)
            ->whereHas('clinicas', function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('medicos', function ($query) {
                $query->where('nombremedico', 'like', '%' . $this->search . '%');
            })
            ->where('estado_solicitud_id', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.entrantes', compact('entrantes'));
    }

    public function openModal($id)
    {
        dd($id);

    }


}
