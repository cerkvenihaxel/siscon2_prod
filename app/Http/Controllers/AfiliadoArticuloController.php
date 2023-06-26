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
        $count = AfiliadosArticulosModel::count()->distinct('nro_afiliado');

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

    public function edit($id)
    {
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);
        return view('afiliadosarticulos.edit', compact('afiliadoArticulo'));
    }


    public function show($id){
        $afiliado = AfiliadosArticulosModel::findOrFail($id);
        $monodroga = DB::table('articulosZafiro')->where('id_articulo', $afiliado->id_articulo)->value('des_monodroga');
        return view('afiliadosarticulos.show', compact('afiliado', 'monodroga'));
    }




    public function destroy($id)
    {
        // Obtener el afiliado y realizar las operaciones de eliminación necesarias
        $afiliadoArticulo = AfiliadosArticulosModel::findOrFail($id);
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
        $searchTerm = $request->input('term');
        $results = Afiliados::where('apeynombres', 'LIKE', '%' . $searchTerm . '%')->orWhere('documento', 'LIKE', '%' . $searchTerm . '%' )->orWhere('nroAfiliado', 'LIKE',  '%' . $searchTerm . '%')->get(); // Reemplaza 'column_name' con el nombre de la columna en la tabla que deseas buscar

        $data = [];

        foreach ($results as $result) {
            $data[] = [
                'id' => $result->nroAfiliado,
                'text' => $result->apeynombres, // Reemplaza 'name' con el nombre de la columna que deseas mostrar en el campo de búsqueda
            ];
        }

        return response()->json($data);
    }

    public function getArticulos(Request $request)
    {
        $searchTerm = $request->input('term');
        $results = ArticulosZafiro::where('des_monodroga', 'LIKE', '%' . $searchTerm . '%')->orWhere('presentacion_completa', 'LIKE', '%' .$searchTerm . '%')->get(); // Reemplaza 'column_name' con el nombre de la columna en la tabla que deseas buscar

        $data = [];

        foreach ($results as $result) {
            $data[] = [
                'id' => $result->id_articulo,
                'text' => $result->presentacion_completa, // Reemplaza 'name' con el nombre de la columna que deseas mostrar en el campo de búsqueda
            ];
        }

        return response()->json($data);
    }

    public function guardarFilas(Request $request)
    {

        $filas = $request->input('filas');




        // Recorrer las filas y guardarlas en la base de datos
        foreach ($filas as $fila) {
            // Crear una nueva instancia del modelo ArticulosAfiliadosModel
            $articuloAfiliado = new AfiliadosArticulosModel();
            // Asignar los valores de la fila al modelo
            $articuloAfiliado->nro_afiliado = $fila['nro_afiliado'];
            $articuloAfiliado->nombre = Afiliados::where('nroAfiliado', $fila['nro_afiliado'])->value('apeynombres');
            $articuloAfiliado->id_articulo = $fila['id_articulo'];
            $articuloAfiliado->des_articulo = ArticulosZafiro::where('id_articulo', $fila['id_articulo'])->value('des_articulo');
            $articuloAfiliado->presentacion = Articul543212341osZafiro::where('id_articulo', $fila['id_articulo'])->value('presentacion');
            $articuloAfiliado->patologias = $fila['patologias'];
            $articuloAfiliado->cantidad = $fila['cantidad'];

            // Guardar el modelo en la base de datos
            $articuloAfiliado->save();
        }
        // Ejemplo de respuesta del controlador
        return response()->json(['success' => true]);
    }
}
