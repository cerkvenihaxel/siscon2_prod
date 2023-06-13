<?php

namespace App\Http\Controllers;

use App\Models\AfiliadosArticulosModel;
use Illuminate\Http\Request;


class AfiliadoArticuloController extends Controller
{
    public function index()
    {
        $afiliadosArticulos = AfiliadosArticulosModel::all();
        return view('afiliadosArticulosView', compact('afiliadosArticulos'));
    }

    public function store(Request $request)
    {
        $afiliadoArticulo = AfiliadosArticulosModel::create($request->all());
        // Realizar las validaciones y el manejo de errores si es necesario
        return redirect()->back()->with('success', 'Registro creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);
        $afiliadoArticulo->update($request->all());
        // Realizar las validaciones y el manejo de errores si es necesario
        return redirect()->back()->with('success', 'Registro actualizado exitosamente.');
    }
}
