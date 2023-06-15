<?php

namespace App\Http\Controllers;

use App\Models\AfiliadosArticulosModel;
use App\Models\ArticulosZafiro;
use Illuminate\Http\Request;
use App\Models\Afiliados;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class AfiliadoArticuloController extends Controller
{
    public function index()
    {
        $afiliadosArticulos = AfiliadosArticulosModel::orderBy('id', 'desc')->paginate(20);
        $count = AfiliadosArticulosModel::count();

        $data = [
            'afiliados' => DB::table('afiliados')->get(),
            'articulos' => ArticulosZafiro::where('id_familia', '=', '01'),
            'patologias' => DB::table('patologias')->get(),
        ];


        return view('afiliadosArticulosView', compact('afiliadosArticulos', 'count', 'data'));
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
        $monodroga = DB::table('articulosZafiro')->where('id_articulo', $afiliado->id_articulo)->value('des_monodroga');
        return view('afiliadosarticulos.show', compact('afiliado', 'monodroga'));
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
        return redirect()->route('afiliados_articulos.index')->with('success', 'Registro eliminado exitosamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $afiliadosArticulos = DB::table('afiliados_articulos')
            ->where('nro_afiliado', 'LIKE', '%' . $search . '%')
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
            ->orderBy('id', 'desc')
            ->paginate(20);

        $count = DB::table('afiliados_articulos')
            ->where('nro_afiliado', 'LIKE', '%' . $search . '%')
            ->orWhere('nombre', 'LIKE', '%' . $search . '%')
            ->orWhere('des_articulo', 'LIKE', '%' . $search . '%')
            ->orWhere('presentacion', 'LIKE', '%' . $search . '%')
            ->orWhere('cantidad', 'LIKE', '%' . $search . '%')
            ->orWhereExists(function ($query) use ($search) {
                $query->select(DB::raw(1))
                    ->from('patologias')
                    ->whereRaw('patologias.id = afiliados_articulos.patologias')
                    ->where('nombre', 'LIKE', '%' . $search . '%');
            })->count();

        return view('afiliadosArticulosView', [
            'afiliadosArticulos' => $afiliadosArticulos->appends(['search' => $search]),
            'count' => $count,
        ]);

    }

    public function getAfiliados(Request $request)
    {
        $term = $request->input('term');

        // Verificar si los resultados están en caché
        $cacheKey = 'afiliados_' . $term;
        if (Cache::has($cacheKey)) {
            $results = Cache::get($cacheKey);
        } else {
            // Consulta optimizada para obtener los afiliados filtrados
            $results = DB::table('afiliados')
                ->where('apeynombres', 'LIKE', "%{$term}%")
                ->take(10)
                ->get();

            // Almacenar los resultados en caché por 5 minutos
            Cache::put($cacheKey, $results, 300);
        }

        return response()->json($results);
    }

    public function guardarFilas(Request $request)
    {
        $filas = $request->input('filas');

        // Recorrer las filas y guardarlas en la base de datos
        foreach ($filas as $fila) {
            AfiliadosArticulosModel::create($fila);
        }
        // Ejemplo de respuesta del controlador
        return response()->json(['success' => true]);
    }
}
