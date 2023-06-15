<?php

namespace App\Http\Controllers;

use App\Models\AfiliadosArticulosModel;
use Illuminate\Http\Request;
use App\Models\Afiliados;
use Illuminate\Support\Facades\DB;


class AfiliadoArticuloController extends Controller
{
    public function index()
    {
        $afiliadosArticulos = AfiliadosArticulosModel::paginate(20);
        $count = $afiliadosArticulos->count();
        return view('afiliadosArticulosView', compact('afiliadosArticulos', 'count'));
    }

    public function store(Request $request)
    {
        $afiliadoArticulo = AfiliadosArticulosModel::create($request->all());
        // Realizar las validaciones y el manejo de errores si es necesario
        return redirect()->back()->with('success', 'Registro creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        // Validar los datos del formulario si es necesario
        $request->validate([
            'cantidad' => 'required|integer',
        ]);

        // Obtener el afiliado y sus datos actuales
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);

        // Actualizar la cantidad solamente, los demás valores permanecen iguales
        $afiliadoArticulo->cantidad = $request->cantidad;
        $afiliadoArticulo->save();

        // Redireccionar a la página de visualización de afiliados o a donde corresponda
        return redirect()->route('afiliados_articulos.index')->with('success', 'Datos actualizados exitosamente');
    }

    public function show($id){
        $afiliado = AfiliadosArticulosModel::findOrFail($id);
        return view('afiliadosarticulos.show', compact('afiliado'));
    }

    public function edit($id)
    {
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);
        return view('afiliadosarticulos.edit', compact('afiliadoArticulo'));
    }



    public function destroy($id)
    {
        // Obtener el afiliado y realizar las operaciones de eliminación necesarias
        $afiliadoArticulo = AfiliadosArticulosModel::find($id);
        // ...

        // Eliminar el afiliado
        $afiliadoArticulo->delete();

        // Redireccionar a la vista de lista de afiliados o a donde sea necesario
        return redirect()->route('afiliados_articulos.index')->with('success', 'Afiliado eliminado exitosamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $afiliadosArticulos = DB::table('afiliados_articulos')
            ->where('nroAfiliado', 'LIKE', '%' . $search . '%')
            ->orWhere('nombre', 'LIKE', '%' . $search . '%')
            ->orWhere('des_articulo', 'LIKE', '%' . $search . '%')
            ->orWhere('presentacion', 'LIKE', '%' . $search . '%')
            ->orWhere('cantidad', 'LIKE', '%' . $search . '%')
            ->orWhereExists(function ($query) use ($search) {
                $query->select(DB::raw(1))
                    ->from('patologias')
                    ->whereRaw('patologias.id = afiliados_articulos.patologias')
                    ->where('nombre', 'LIKE', '%' . $search . '%');
            })
            ->paginate(20);
        $count = $afiliadosArticulos->count();

        return view('afiliadosArticulosView', compact('afiliadosArticulos', 'count'));
    }
}
