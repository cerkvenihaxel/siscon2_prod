<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpConsumo;
use App\Models\UpEntrega;
use CRUDBooster;
use Storage;

class EntregasUpController extends Controller
{
    public function index(Request $request)
    {
        $query = UpEntrega::with('consumo')->orderBy('fecha_entrega', 'desc');

        // Filtro por usuario: Solo Super Admin y Administrador General ven todas
        if (!CRUDBooster::isSuperadmin() && CRUDBooster::myPrivilegeName() != 'Administrador General') {
            $query->where('usuario_entrega', CRUDBooster::myName());
        }

        // Filtros
        if ($request->codigo_afiliado) {
            $query->where('codigo_afiliado', 'like', '%' . $request->codigo_afiliado . '%');
        }
        if ($request->nombre_afiliado) {
            $query->where('nombre_afiliado', 'like', '%' . $request->nombre_afiliado . '%');
        }
        if ($request->estado_entrega) {
            $query->where('estado_entrega', $request->estado_entrega);
        }
        if ($request->fecha_desde) {
            $query->whereDate('fecha_entrega', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_entrega', '<=', $request->fecha_hasta);
        }

        $entregas = $query->paginate(20);

        // Contadores con filtro por usuario
        $contadoresQuery = UpEntrega::query();
        if (!CRUDBooster::isSuperadmin() && CRUDBooster::myPrivilegeName() != 'Administrador General') {
            $contadoresQuery->where('usuario_entrega', CRUDBooster::myName());
        }

        $contadores = [
            'total' => (clone $contadoresQuery)->count(),
            'completas' => (clone $contadoresQuery)->where('estado_entrega', 'completa')->count(),
            'parciales' => (clone $contadoresQuery)->where('estado_entrega', 'parcial')->count(),
            'rechazadas' => (clone $contadoresQuery)->where('estado_entrega', 'rechazada')->count(),
        ];

        return view('entregas_up.index', compact('entregas', 'contadores'));
    }

    public function add($consumo_id)
    {
        try {
            $consumo = UpConsumo::findOrFail($consumo_id);

            // Verificar que el usuario puede entregar este consumo
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $consumo->usuario_aprobacion != CRUDBooster::myName()) {
                return redirect('/admin/consumos-up')->with('error', 'No tiene permisos para entregar este consumo');
            }

            // Verificar que el consumo esté aprobado
            if ($consumo->estado_flujo !== 'aprobado') {
                return redirect('/admin/consumos-up')
                    ->with('error', 'Solo se pueden entregar consumos aprobados');
            }

            return view('entregas_up.add', compact('consumo'));
        } catch (\Exception $e) {
            return redirect('/admin/consumos-up')
                ->with('error', 'Consumo no encontrado');
        }
    }

    public function imprimirFormulario($consumo_id)
    {
        try {
            $consumo = UpConsumo::findOrFail($consumo_id);
            return view('entregas_up.imprimir_formulario', compact('consumo'));
        } catch (\Exception $e) {
            return redirect('/admin/consumos-up')
                ->with('error', 'Consumo no encontrado');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'consumo_id' => 'required|exists:up_consumos,id',
                'fecha_entrega' => 'required|date',
                'cantidad_entregada' => 'required|integer|min:1',
                'estado_entrega' => 'required|in:completa,parcial,rechazada',
                'quien_recibe' => 'required|string|max:200',
                'dni_afiliado' => 'nullable|string|max:20',
            ]);

            $consumo = UpConsumo::findOrFail($request->consumo_id);

            // Procesar archivos adjuntos
            $archivosAdjuntos = [];
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $path = $archivo->store('entregas_up', 'public');
                    $archivosAdjuntos[] = [
                        'nombre' => $archivo->getClientOriginalName(),
                        'path' => $path,
                        'fecha' => now()->toDateTimeString(),
                    ];
                }
            }

            // Procesar firma si existe
            $firmaPath = null;
            if ($request->has('firma_base64') && !empty($request->firma_base64)) {
                $firmaData = $request->firma_base64;
                $firmaData = str_replace('data:image/png;base64,', '', $firmaData);
                $firmaData = str_replace(' ', '+', $firmaData);
                $firmaDecoded = base64_decode($firmaData);

                $firmaPath = 'entregas_up/firmas/' . uniqid() . '.png';
                Storage::disk('public')->put($firmaPath, $firmaDecoded);
            }

            // Crear registro de entrega
            $entrega = UpEntrega::create([
                'consumo_id' => $consumo->id,
                'codigo_afiliado' => $consumo->afiliado,
                'nombre_afiliado' => trim($consumo->nombres . ' ' . $consumo->apellidos),
                'cod_prestacion' => $consumo->cod_prestacion,
                'descripcion_prestacion' => $consumo->desc,
                'cantidad_solicitada' => $consumo->cant,
                'cantidad_entregada' => $request->cantidad_entregada,
                'fecha_entrega' => $request->fecha_entrega,
                'estado_entrega' => $request->estado_entrega,
                'observaciones' => $request->observaciones,
                'usuario_entrega' => auth()->check() ? auth()->user()->email : (CRUDBooster::myName() ?? 'sistema'),
                'archivos_adjuntos' => $archivosAdjuntos,
                'firma_afiliado' => $firmaPath,
                'dni_afiliado' => $request->dni_afiliado,
                'remito' => $request->remito,
                'transporte' => $request->transporte,
                'quien_recibe' => $request->quien_recibe,
                'relacion_afiliado' => $request->relacion_afiliado,
            ]);

            // Actualizar estado del consumo
            $consumo->update([
                'estado_flujo' => 'entregado',
                'fecha_entrega' => $request->fecha_entrega,
                'usuario_entrega' => auth()->check() ? auth()->user()->email : (CRUDBooster::myName() ?? 'sistema'),
            ]);

            return redirect('/admin/entregas-up/' . $entrega->id . '/consentimiento')
                ->with('success', 'Entrega registrada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar entrega: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function consentimiento($id)
    {
        try {
            $entrega = UpEntrega::with('consumo')->findOrFail($id);
            
            // Verificar que el usuario puede ver esta entrega
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $entrega->usuario_entrega != CRUDBooster::myName()) {
                return redirect('/admin/entregas-up')->with('error', 'No tiene permisos para ver esta entrega');
            }
            
            return view('entregas_up.consentimiento', compact('entrega'));
        } catch (\Exception $e) {
            return redirect('/admin/consumos-up')
                ->with('error', 'Entrega no encontrada');
        }
    }

    public function generarPDF($id)
    {
        try {
            $entrega = UpEntrega::with('consumo')->findOrFail($id);

            // Verificar que el usuario puede generar PDF de esta entrega
            if (!CRUDBooster::isSuperadmin() && 
                CRUDBooster::myPrivilegeName() != 'Administrador General' && 
                $entrega->usuario_entrega != CRUDBooster::myName()) {
                return response()->json(['success' => false, 'message' => 'No tiene permisos para generar PDF de esta entrega'], 403);
            }

            // Guardar referencia del archivo PDF
            $pdfFilename = 'consentimiento_' . $entrega->id . '_' . time() . '.pdf';
            $entrega->update([
                'archivo_consentimiento' => $pdfFilename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF generado exitosamente',
                'filename' => $pdfFilename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ]);
        }
    }
}
