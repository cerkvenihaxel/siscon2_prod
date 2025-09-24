<?php

namespace App\Http\Controllers;

use App\Models\PedidoOxigenoterapia;
use App\Models\PrestamoOxigenoterapia;
use App\Models\DocumentosPrestamo;
use App\Models\EquiposOxigenoterapia;
use App\Models\EstadoOxigenoterapia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CRUDBooster;
use Illuminate\Support\Facades\Storage;

class PrestamoOxigenoterapiaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido_oxigenoterapia,id',
            'fecha_inicio_prestamo' => 'required|date',
            'fecha_fin_prestamo' => 'required|date|after:fecha_inicio_prestamo',
            'tipo_direccion' => 'required|in:PARTICULAR,CLINICA',
            'direccion_entrega' => 'required|string|max:255',
            'localidad_entrega' => 'required|string|max:255',
            'provincia_entrega' => 'required|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
            'telefono_contacto' => 'required|string|max:255',
            'nombre_contacto' => 'required|string|max:255',
            'observaciones_entrega' => 'nullable|string',
            'equipo_id' => 'required|exists:equipos_oxigenoterapia,id',
        ]);

        DB::beginTransaction();

        try {
            $pedido = PedidoOxigenoterapia::findOrFail($request->pedido_id);
            $equipo = EquiposOxigenoterapia::findOrFail($request->equipo_id);

            // Verificar que el equipo esté disponible
            if (!$equipo->isDisponible()) {
                throw new \Exception('El equipo seleccionado no está disponible');
            }

            // Crear el préstamo
            $prestamo = new PrestamoOxigenoterapia();
            $prestamo->pedido_oxigenoterapia_id = $request->pedido_id;
            $prestamo->nro_prestamo = $this->generatePrestamoNumber();
            $prestamo->fecha_inicio_prestamo = $request->fecha_inicio_prestamo;
            $prestamo->fecha_fin_prestamo = $request->fecha_fin_prestamo;
            $prestamo->tipo_direccion = $request->tipo_direccion;
            $prestamo->direccion_entrega = $request->direccion_entrega;
            $prestamo->localidad_entrega = $request->localidad_entrega;
            $prestamo->provincia_entrega = $request->provincia_entrega;
            $prestamo->codigo_postal = $request->codigo_postal;
            $prestamo->telefono_contacto = $request->telefono_contacto;
            $prestamo->nombre_contacto = $request->nombre_contacto;
            $prestamo->observaciones_entrega = $request->observaciones_entrega;
            $prestamo->equipo_entregado = $equipo->nombre_equipo;
            $prestamo->nro_serie_equipo = $equipo->nro_serie;
            $prestamo->estado_prestamo = 'ACTIVO';
            $prestamo->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
            $prestamo->save();

            // Actualizar estado del equipo
            $equipo->estado_equipo = 'EN_USO';
            $equipo->save();

            // Actualizar estado del pedido
            $pedido->estado_oxigenoterapia_id = 3; // EN_PRESTAMO
            $pedido->save();

            // Generar documentos
            $this->generarDocumentos($prestamo);

            DB::commit();

            return redirect('/admin/oxigenoterapia')->with('message', 'Préstamo creado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function entregar(Request $request, $id)
    {
        $request->validate([
            'fecha_entrega' => 'required|date',
            'observaciones_entrega' => 'nullable|string',
        ]);

        $prestamo = PrestamoOxigenoterapia::findOrFail($id);
        $prestamo->fecha_entrega = $request->fecha_entrega;
        $prestamo->observaciones_entrega = $request->observaciones_entrega;
        $prestamo->save();

        return redirect('/admin/oxigenoterapia/ver-prestamo/' . $prestamo->pedido_oxigenoterapia_id)
                ->with('message', 'Entrega registrada correctamente');
    }

    public function renovar(Request $request, $id)
    {
        $request->validate([
            'nueva_fecha_fin' => 'required|date|after:today',
            'motivo_renovacion' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $prestamo = PrestamoOxigenoterapia::findOrFail($id);
            $pedido = $prestamo->pedidoOxigenoterapia;

            // Finalizar préstamo actual
            $prestamo->estado_prestamo = 'RENOVADO';
            $prestamo->save();

            // Crear nuevo préstamo
            $nuevoPrestamo = new PrestamoOxigenoterapia();
            $nuevoPrestamo->pedido_oxigenoterapia_id = $prestamo->pedido_oxigenoterapia_id;
            $nuevoPrestamo->nro_prestamo = $this->generatePrestamoNumber();
            $nuevoPrestamo->fecha_inicio_prestamo = $prestamo->fecha_fin_prestamo;
            $nuevoPrestamo->fecha_fin_prestamo = $request->nueva_fecha_fin;
            $nuevoPrestamo->tipo_direccion = $prestamo->tipo_direccion;
            $nuevoPrestamo->direccion_entrega = $prestamo->direccion_entrega;
            $nuevoPrestamo->localidad_entrega = $prestamo->localidad_entrega;
            $nuevoPrestamo->provincia_entrega = $prestamo->provincia_entrega;
            $nuevoPrestamo->codigo_postal = $prestamo->codigo_postal;
            $nuevoPrestamo->telefono_contacto = $prestamo->telefono_contacto;
            $nuevoPrestamo->nombre_contacto = $prestamo->nombre_contacto;
            $nuevoPrestamo->equipo_entregado = $prestamo->equipo_entregado;
            $nuevoPrestamo->nro_serie_equipo = $prestamo->nro_serie_equipo;
            $nuevoPrestamo->estado_prestamo = 'ACTIVO';
            $nuevoPrestamo->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
            $nuevoPrestamo->save();

            // Actualizar estado del pedido
            $pedido->estado_oxigenoterapia_id = 4; // RENOVADO
            $pedido->save();

            // Generar documentos de renovación
            $this->generarDocumentoRenovacion($nuevoPrestamo, $request->motivo_renovacion);

            DB::commit();

            return redirect('/admin/oxigenoterapia')->with('message', 'Préstamo renovado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function finalizar(Request $request, $id)
    {
        $request->validate([
            'fecha_devolucion' => 'required|date',
            'observaciones_devolucion' => 'nullable|string',
            'estado_equipo' => 'required|in:DISPONIBLE,MANTENIMIENTO,RETIRADO',
        ]);

        DB::beginTransaction();

        try {
            $prestamo = PrestamoOxigenoterapia::findOrFail($id);
            $pedido = $prestamo->pedidoOxigenoterapia;
            $equipo = EquiposOxigenoterapia::where('nro_serie', $prestamo->nro_serie_equipo)->first();

            // Finalizar préstamo
            $prestamo->estado_prestamo = 'FINALIZADO';
            $prestamo->fecha_devolucion = $request->fecha_devolucion;
            $prestamo->observaciones_entrega = $request->observaciones_devolucion;
            $prestamo->save();

            // Actualizar estado del equipo
            if ($equipo) {
                $equipo->estado_equipo = $request->estado_equipo;
                $equipo->save();
            }

            // Actualizar estado del pedido
            $pedido->estado_oxigenoterapia_id = 5; // FINALIZADO
            $pedido->save();

            // Generar documento de finalización
            $this->generarDocumentoFinalizacion($prestamo);

            DB::commit();

            return redirect('/admin/oxigenoterapia')->with('message', 'Préstamo finalizado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function firmarDocumento(Request $request, $documento_id)
    {
        $request->validate([
            'firma_digital' => 'required|string',
        ]);

        $documento = DocumentosPrestamo::findOrFail($documento_id);
        $documento->firma_digital = $request->firma_digital;
        $documento->fecha_firma = now();
        $documento->ip_firma = $request->ip();
        $documento->estado_documento = 'FIRMADO';
        $documento->save();

        return response()->json(['success' => true, 'message' => 'Documento firmado correctamente']);
    }

    private function generatePrestamoNumber()
    {
        $lastPrestamo = PrestamoOxigenoterapia::latest()->first();

        if ($lastPrestamo) {
            $lastNumber = substr($lastPrestamo->nro_prestamo, 12);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'PREST-' . date('Ymd') . '-' . $newNumber;
    }

    private function generarDocumentos($prestamo)
    {
        // Generar términos y condiciones
        $this->generarTerminosCondiciones($prestamo);

        // Generar contrato
        $this->generarContrato($prestamo);
    }

    private function generarTerminosCondiciones($prestamo)
    {
        $contenido = $this->getContenidoTerminosCondiciones($prestamo);

        $documento = new DocumentosPrestamo();
        $documento->prestamo_oxigenoterapia_id = $prestamo->id;
        $documento->tipo_documento = 'TERMINOS_CONDICIONES';
        $documento->nombre_documento = 'Términos y Condiciones - ' . $prestamo->nro_prestamo;
        $documento->contenido_documento = $contenido;
        $documento->fecha_generacion = now();
        $documento->estado_documento = 'GENERADO';
        $documento->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
        $documento->save();
    }

    private function generarContrato($prestamo)
    {
        $contenido = $this->getContenidoContrato($prestamo);

        $documento = new DocumentosPrestamo();
        $documento->prestamo_oxigenoterapia_id = $prestamo->id;
        $documento->tipo_documento = 'CONTRATO';
        $documento->nombre_documento = 'Contrato de Préstamo - ' . $prestamo->nro_prestamo;
        $documento->contenido_documento = $contenido;
        $documento->fecha_generacion = now();
        $documento->estado_documento = 'GENERADO';
        $documento->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
        $documento->save();
    }

    private function generarDocumentoRenovacion($prestamo, $motivo)
    {
        $contenido = $this->getContenidoRenovacion($prestamo, $motivo);

        $documento = new DocumentosPrestamo();
        $documento->prestamo_oxigenoterapia_id = $prestamo->id;
        $documento->tipo_documento = 'RENOVACION';
        $documento->nombre_documento = 'Renovación de Préstamo - ' . $prestamo->nro_prestamo;
        $documento->contenido_documento = $contenido;
        $documento->fecha_generacion = now();
        $documento->estado_documento = 'GENERADO';
        $documento->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
        $documento->save();
    }

    private function generarDocumentoFinalizacion($prestamo)
    {
        $contenido = $this->getContenidoFinalizacion($prestamo);

        $documento = new DocumentosPrestamo();
        $documento->prestamo_oxigenoterapia_id = $prestamo->id;
        $documento->tipo_documento = 'FINALIZACION';
        $documento->nombre_documento = 'Finalización de Préstamo - ' . $prestamo->nro_prestamo;
        $documento->contenido_documento = $contenido;
        $documento->fecha_generacion = now();
        $documento->estado_documento = 'GENERADO';
        $documento->stamp_user = DB::table('cms_users')->where('id', CRUDBooster::myId())->value('email');
        $documento->save();
    }

    private function getContenidoTerminosCondiciones($prestamo)
    {
        $pedido = $prestamo->pedidoOxigenoterapia;
        
        return "
        <h2>TÉRMINOS Y CONDICIONES DE PRÉSTAMO DE EQUIPO DE OXIGENOTERAPIA</h2>
        
        <p><strong>Número de Préstamo:</strong> {$prestamo->nro_prestamo}</p>
        <p><strong>Fecha de Generación:</strong> " . now()->format('d/m/Y') . "</p>
        
        <h3>DATOS DEL PACIENTE</h3>
        <p><strong>Nombre:</strong> {$pedido->nombre_apellido}</p>
        <p><strong>Número de Afiliado:</strong> {$pedido->nro_afiliado}</p>
        <p><strong>Documento:</strong> {$pedido->documento}</p>
        <p><strong>Edad:</strong> {$pedido->edad} años</p>
        
        <h3>DATOS DEL EQUIPO</h3>
        <p><strong>Equipo:</strong> {$prestamo->equipo_entregado}</p>
        <p><strong>Número de Serie:</strong> {$prestamo->nro_serie_equipo}</p>
        
        <h3>CONDICIONES DEL PRÉSTAMO</h3>
        <p><strong>Fecha de Inicio:</strong> {$prestamo->fecha_inicio_prestamo->format('d/m/Y')}</p>
        <p><strong>Fecha de Finalización:</strong> {$prestamo->fecha_fin_prestamo->format('d/m/Y')}</p>
        
        <h3>DIRECCIÓN DE ENTREGA</h3>
        <p><strong>Tipo:</strong> {$prestamo->tipo_direccion}</p>
        <p><strong>Dirección:</strong> {$prestamo->direccion_entrega}</p>
        <p><strong>Localidad:</strong> {$prestamo->localidad_entrega}</p>
        <p><strong>Provincia:</strong> {$prestamo->provincia_entrega}</p>
        <p><strong>Contacto:</strong> {$prestamo->nombre_contacto} - {$prestamo->telefono_contacto}</p>
        
        <h3>CONDICIONES GENERALES</h3>
        <ol>
            <li>El equipo debe ser utilizado únicamente por el paciente autorizado.</li>
            <li>El equipo debe mantenerse en buen estado y no debe ser modificado.</li>
            <li>En caso de mal funcionamiento, contactar inmediatamente al servicio técnico.</li>
            <li>El equipo debe ser devuelto en la fecha establecida.</li>
            <li>El paciente es responsable del cuidado y mantenimiento básico del equipo.</li>
        </ol>
        
        <p><strong>Firma del Paciente:</strong> _________________________</p>
        <p><strong>Fecha:</strong> _________________________</p>
        ";
    }

    private function getContenidoContrato($prestamo)
    {
        $pedido = $prestamo->pedidoOxigenoterapia;
        
        return "
        <h2>CONTRATO DE PRÉSTAMO DE EQUIPO DE OXIGENOTERAPIA</h2>
        
        <p><strong>Número de Contrato:</strong> {$prestamo->nro_prestamo}</p>
        <p><strong>Fecha de Contrato:</strong> " . now()->format('d/m/Y') . "</p>
        
        <h3>PARTES CONTRATANTES</h3>
        <p><strong>PRESTADOR:</strong> [Nombre de la Institución]</p>
        <p><strong>PACIENTE:</strong> {$pedido->nombre_apellido}</p>
        
        <h3>OBJETO DEL CONTRATO</h3>
        <p>El presente contrato tiene por objeto el préstamo del equipo de oxigenoterapia especificado para uso domiciliario del paciente.</p>
        
        <h3>ESPECIFICACIONES DEL EQUIPO</h3>
        <p><strong>Equipo:</strong> {$prestamo->equipo_entregado}</p>
        <p><strong>Número de Serie:</strong> {$prestamo->nro_serie_equipo}</p>
        
        <h3>PLAZO DEL CONTRATO</h3>
        <p><strong>Inicio:</strong> {$prestamo->fecha_inicio_prestamo->format('d/m/Y')}</p>
        <p><strong>Finalización:</strong> {$prestamo->fecha_fin_prestamo->format('d/m/Y')}</p>
        
        <h3>OBLIGACIONES DEL PACIENTE</h3>
        <ul>
            <li>Utilizar el equipo según las indicaciones médicas</li>
            <li>Mantener el equipo en buen estado</li>
            <li>Reportar cualquier mal funcionamiento</li>
            <li>Devolver el equipo al finalizar el contrato</li>
        </ul>
        
        <h3>OBLIGACIONES DEL PRESTADOR</h3>
        <ul>
            <li>Entregar el equipo en buen estado</li>
            <li>Proporcionar mantenimiento técnico</li>
            <li>Brindar soporte y asesoramiento</li>
        </ul>
        
        <p><strong>Firma del Paciente:</strong> _________________________</p>
        <p><strong>Firma del Representante:</strong> _________________________</p>
        <p><strong>Fecha:</strong> _________________________</p>
        ";
    }

    private function getContenidoRenovacion($prestamo, $motivo)
    {
        $pedido = $prestamo->pedidoOxigenoterapia;
        
        return "
        <h2>RENOVACIÓN DE CONTRATO DE PRÉSTAMO</h2>
        
        <p><strong>Número de Renovación:</strong> {$prestamo->nro_prestamo}</p>
        <p><strong>Fecha de Renovación:</strong> " . now()->format('d/m/Y') . "</p>
        
        <h3>MOTIVO DE LA RENOVACIÓN</h3>
        <p>{$motivo}</p>
        
        <h3>NUEVAS FECHAS</h3>
        <p><strong>Nuevo Inicio:</strong> {$prestamo->fecha_inicio_prestamo->format('d/m/Y')}</p>
        <p><strong>Nueva Finalización:</strong> {$prestamo->fecha_fin_prestamo->format('d/m/Y')}</p>
        
        <p><strong>Firma del Paciente:</strong> _________________________</p>
        <p><strong>Fecha:</strong> _________________________</p>
        ";
    }

    private function getContenidoFinalizacion($prestamo)
    {
        $pedido = $prestamo->pedidoOxigenoterapia;
        
        return "
        <h2>ACTA DE FINALIZACIÓN DE PRÉSTAMO</h2>
        
        <p><strong>Número de Préstamo:</strong> {$prestamo->nro_prestamo}</p>
        <p><strong>Fecha de Finalización:</strong> " . now()->format('d/m/Y') . "</p>
        
        <h3>CONFIRMACIÓN DE DEVOLUCIÓN</h3>
        <p><strong>Equipo Devuelto:</strong> {$prestamo->equipo_entregado}</p>
        <p><strong>Número de Serie:</strong> {$prestamo->nro_serie_equipo}</p>
        <p><strong>Fecha de Devolución:</strong> {$prestamo->fecha_devolucion->format('d/m/Y')}</p>
        
        <h3>ESTADO DEL EQUIPO</h3>
        <p><strong>Observaciones:</strong> {$prestamo->observaciones_entrega}</p>
        
        <p><strong>Firma del Paciente:</strong> _________________________</p>
        <p><strong>Firma del Representante:</strong> _________________________</p>
        <p><strong>Fecha:</strong> _________________________</p>
        ";
    }
} 