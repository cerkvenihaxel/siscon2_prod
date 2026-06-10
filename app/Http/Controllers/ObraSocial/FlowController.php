<?php

namespace App\Http\Controllers\ObraSocial;

use App\Http\Controllers\Controller;
use App\Models\Drogueria\ObraSocial;
use App\Models\Drogueria\ObraSocialConsumo;
use App\Services\TwilioSender;
use App\Support\ObraSocial\EstadoConsumo;
use Barryvdh\DomPDF\Facade as PDF;
use CRUDBooster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Controlador base, reutilizable y escalable, del flujo de consumos de obra social.
 *
 * El flujo replica al de Unión Personal pero leyendo de drogueria.osplad_consumos:
 *   PENDIENTES  -> En tránsito -> Entregas
 *
 * Una nueva obra social solo necesita una subclase que defina osCodigo()/rutaBase()/titulo(),
 * sus filas en osplad_consumos con el os_id correspondiente, y rutas/menús.
 */
abstract class FlowController extends Controller
{
    protected TwilioSender $twilio;
    private ?ObraSocial $osActiva = null;

    public function __construct()
    {
        $this->twilio = new TwilioSender();
    }

    /* ----------------------------------------------------------------------
     | A definir por cada obra social
     | ---------------------------------------------------------------------- */
    abstract protected function osCodigo(): string;   // ej: 'OSPLAD' (OS por defecto)
    abstract protected function rutaBase(): string;    // ej: '/admin/osplad'
    abstract protected function titulo(): string;      // ej: 'OSPLAD'

    /* ----------------------------------------------------------------------
     | Resolución de obra social y query base (con filtro por farmacia/rol)
     | ---------------------------------------------------------------------- */

    /** Obra social por defecto del controller (fallback). */
    protected function obraSocialDefault(): ObraSocial
    {
        $os = ObraSocial::porCodigo($this->osCodigo());
        abort_if($os === null, 500, 'Obra social no configurada: ' . $this->osCodigo());
        return $os;
    }

    /**
     * Obra social activa:
     * - Admin (Super Admin / Admin General): según el selector (request 'os_id' o sesión).
     * - Farmacia: la que tenga asignada (derivada de sus consumos por id_cliente).
     * En ambos casos, si no se resuelve, cae a la OS por defecto del controller.
     */
    protected function obraSocialActiva(): ObraSocial
    {
        if ($this->osActiva !== null) {
            return $this->osActiva;
        }

        if ($this->veTodo()) {
            if (request()->filled('os_id')) {
                session(['osplad_os_id' => request('os_id')]);
                session()->forget('osplad_farmacias'); // al cambiar de OS, reiniciar farmacias
            }
            $osId = session('osplad_os_id');
            $this->osActiva = $osId ? ObraSocial::where('activo', true)->find($osId) : null;
        } else {
            // Farmacia: derivar la OS de sus propios consumos
            $osId = ObraSocialConsumo::deFarmacia($this->idClienteUsuario())
                ->whereNotNull('os_id')->value('os_id');
            $this->osActiva = $osId ? ObraSocial::find($osId) : null;
        }

        if ($this->osActiva === null) {
            $this->osActiva = $this->obraSocialDefault();
        }
        return $this->osActiva;
    }

    /**
     * Query base filtrada por obra social activa y por farmacia según el rol:
     * - Farmacia: solo su id_cliente.
     * - Admin: todas, o las farmacias elegidas en el selector.
     */
    protected function baseQuery(): Builder
    {
        $query = ObraSocialConsumo::query()->deObraSocial($this->obraSocialActiva()->id);

        if (!$this->veTodo()) {
            $query->deFarmacia($this->idClienteUsuario());
        } else {
            $farmacias = $this->farmaciasSeleccionadas();
            if (!empty($farmacias)) {
                $query->whereIn('id_cliente', $farmacias);
            }
        }

        return $query;
    }

    protected function veTodo(): bool
    {
        return CRUDBooster::isSuperadmin()
            || CRUDBooster::myPrivilegeName() === 'Administrador General';
    }

    protected function idClienteUsuario()
    {
        $me = CRUDBooster::me();
        // Si no tiene farmacia asignada, devolvemos un valor imposible para no filtrar datos ajenos.
        return $me->id_cliente ?? -1;
    }

    /** Farmacias elegidas por el admin (request o sesión). Vacío = todas. */
    protected function farmaciasSeleccionadas(): array
    {
        if (request()->has('farmacias')) {
            $sel = (array) request('farmacias');
            $sel = array_values(array_filter(array_map('intval', $sel)));
            session(['osplad_farmacias' => $sel]);
            return $sel;
        }
        return (array) session('osplad_farmacias', []);
    }

    /** Catálogo de obras sociales activas (para el selector del admin). */
    protected function obrasSocialesDisponibles()
    {
        return ObraSocial::where('activo', true)->orderBy('nombre')->get();
    }

    /** Farmacias con consumos en la OS activa (para el selector del admin). */
    protected function farmaciasDisponibles(int $osId)
    {
        return ObraSocialConsumo::deObraSocial($osId)
            ->whereNotNull('id_cliente')->where('id_cliente', '!=', 0)
            ->select('id_cliente', 'cliente')->distinct()
            ->orderBy('cliente')->get();
    }

    /* ----------------------------------------------------------------------
     | Vistas de cada etapa
     | ---------------------------------------------------------------------- */

    public function pendientes(Request $request)
    {
        return $this->indexEtapa($request, EstadoConsumo::ETAPA_PENDIENTES);
    }

    public function transito(Request $request)
    {
        return $this->indexEtapa($request, EstadoConsumo::ETAPA_TRANSITO);
    }

    public function entregas(Request $request)
    {
        return $this->indexEtapa($request, EstadoConsumo::ETAPA_ENTREGAS);
    }

    /** Columna de fecha relevante por etapa (entregas usa la fecha de entrega). */
    protected function columnaFecha(string $etapa): string
    {
        return $etapa === EstadoConsumo::ETAPA_ENTREGAS ? 'fecha_validacion' : 'fecha';
    }

    protected function aplicarFiltros($query, Request $request, string $etapa)
    {
        $colFecha = $this->columnaFecha($etapa);

        if ($request->filled('afiliado')) {
            $query->where('afiliado', 'like', '%' . $request->afiliado . '%');
        }
        if ($request->filled('dni')) {
            $query->where('dni', $request->dni);
        }
        if ($request->filled('articulo')) {
            $query->where('articulo', 'like', '%' . $request->articulo . '%');
        }
        if ($request->filled('remito')) {
            $query->where('id_remito', $request->remito);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate($colFecha, '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate($colFecha, '<=', $request->fecha_hasta);
        }
        return $query;
    }

    protected function indexEtapa(Request $request, string $etapa)
    {
        $query = $this->aplicarFiltros($this->baseQuery()->enEtapa($etapa), $request, $etapa);

        $consumos = $query->orderByDesc($this->columnaFecha($etapa))->orderBy('afiliado')->orderBy('id_consumo')
            ->paginate(20)->withQueryString();

        $osActiva = $this->obraSocialActiva();

        $cfg = [
            'titulo'    => $osActiva->nombre,   // el título refleja la OS activa
            'ruta_base' => $this->rutaBase(),
            'etapa'     => $etapa,
            've_todo'   => $this->veTodo(),
            // Datos para el selector (solo admin)
            'os_activa_id'    => $osActiva->id,
            'obras_sociales'  => $this->veTodo() ? $this->obrasSocialesDisponibles() : collect(),
            'farmacias'       => $this->veTodo() ? $this->farmaciasDisponibles($osActiva->id) : collect(),
            'farmacias_sel'   => $this->veTodo() ? $this->farmaciasSeleccionadas() : [],
        ];

        $data = [
            'consumos'   => $consumos,
            'contadores' => $this->contadores(),
            'cfg'        => $cfg,
        ];

        // En Entregas: resumen con filtros desde-hasta para facturación.
        if ($etapa === EstadoConsumo::ETAPA_ENTREGAS) {
            $data['resumen'] = $this->resumenEntregas($request);
        }

        return view('obra_social.index', $data);
    }

    protected function contadores(): array
    {
        return [
            'pendientes' => $this->baseQuery()->enEtapa(EstadoConsumo::ETAPA_PENDIENTES)->count(),
            'transito'   => $this->baseQuery()->enEtapa(EstadoConsumo::ETAPA_TRANSITO)->count(),
            'entregas'   => $this->baseQuery()->enEtapa(EstadoConsumo::ETAPA_ENTREGAS)->count(),
        ];
    }

    /**
     * Resumen de entregas (con filtros desde-hasta) para facturación.
     * Devuelve cantidad de pedidos entregados, remitos distintos y afiliados.
     */
    protected function resumenEntregas(Request $request): array
    {
        $q = $this->aplicarFiltros(
            $this->baseQuery()->enEtapa(EstadoConsumo::ETAPA_ENTREGAS),
            $request,
            EstadoConsumo::ETAPA_ENTREGAS
        );

        return [
            'pedidos'   => (clone $q)->count(),
            'remitos'   => (clone $q)->distinct()->count('id_remito'),
            'afiliados' => (clone $q)->distinct()->count('dni'),
            'desde'     => $request->fecha_desde,
            'hasta'     => $request->fecha_hasta,
        ];
    }

    /** Localiza un consumo respetando el filtro por farmacia (seguridad). */
    protected function buscarConsumo($id): ObraSocialConsumo
    {
        return $this->baseQuery()->where('id_consumo', $id)->firstOrFail();
    }

    /* ----------------------------------------------------------------------
     | PENDIENTES: ver / imprimir detalle (la farmacia no opera, solo consulta)
     | ---------------------------------------------------------------------- */

    public function detalle(Request $request, $id)
    {
        $consumo = $this->buscarConsumo($id);

        $cfg = [
            'titulo'    => $this->obraSocialActiva()->nombre,
            'ruta_base' => $this->rutaBase(),
        ];

        // ?print=1 abre la versión imprimible
        $print = $request->boolean('print');

        return view($print ? 'obra_social.detalle_print' : 'obra_social.detalle', compact('consumo', 'cfg'));
    }

    /* ----------------------------------------------------------------------
     | EN TRÁNSITO: entrega unificada (un solo remito para varios pedidos)
     | ---------------------------------------------------------------------- */

    /**
     * Marca como entregados uno o varios consumos del MISMO afiliado y MISMO remito.
     * Dispara UN mensaje de entrega (notif #2) y deja todo listo para el consentimiento.
     */
    public function entregar(Request $request)
    {
        try {
            $ids = (array) $request->input('ids', []);
            $ids = array_filter(array_map('intval', $ids));

            if (empty($ids)) {
                return $this->error('Seleccione al menos un pedido para entregar.', 422);
            }

            $consumos = $this->baseQuery()->whereIn('id_consumo', $ids)->get();

            if ($consumos->isEmpty()) {
                return $this->error('No se encontraron los pedidos seleccionados.', 404);
            }
            if ($consumos->contains(fn($c) => !$c->esEnTransito())) {
                return $this->error('Solo se pueden entregar pedidos que estén En tránsito.', 422);
            }

            // Entrega unificada: mismo afiliado (dni) y mismo remito
            if ($consumos->pluck('dni')->unique()->count() > 1) {
                return $this->error('La entrega unificada requiere que todos los pedidos sean del mismo afiliado.', 422);
            }
            if ($consumos->pluck('id_remito')->unique()->count() > 1) {
                return $this->error('La entrega unificada requiere que todos los pedidos tengan el mismo remito.', 422);
            }

            $primero = $consumos->first();
            $fechaEntrega = now();
            $pedidoRef = $primero->id_remito ?: ($primero->id_pedido_zafiro ?: ('#' . $primero->id_consumo));

            foreach ($consumos as $c) {
                $c->estado_pedido    = EstadoConsumo::ENTREGADO;
                $c->fecha_validacion = $fechaEntrega;
                $c->save();
            }

            // Una sola notificación de entrega para el afiliado
            $this->twilio->sendObraSocialEntregado(
                $primero->telefono,
                $pedidoRef,
                $fechaEntrega->format('d/m/Y'),
                $primero->cliente,
                $primero->id_consumo
            );

            return $this->ok('Entrega registrada (' . $consumos->count() . ' ítem/s). Se notificó al afiliado.', [
                'redirect' => $this->rutaBase() . '/consentimiento?ids=' . implode(',', $ids),
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Consentimiento de entrega en PDF (similar al de Entregas UP, sin precios y
     * con el nombre de la obra social). Una entrega unificada = un consentimiento.
     */
    public function consentimiento(Request $request)
    {
        $ids = array_filter(array_map('intval', explode(',', (string) $request->query('ids'))));
        $consumos = $this->baseQuery()->whereIn('id_consumo', $ids)->get();

        abort_if($consumos->isEmpty(), 404);

        $os = $this->obraSocialActiva();
        $primero = $consumos->first();
        $remito = $primero->id_remito ?: ($primero->id_pedido_zafiro ?: ('CONS-' . $primero->id_consumo));

        $pdf = PDF::loadView('obra_social.consentimiento_pdf', [
            'consumos'         => $consumos,
            'obraSocialNombre' => $os->nombre,
            'remito'           => $remito,
        ])->setPaper('a4');

        return $pdf->stream('consentimiento_' . $os->codigo . '_' . $remito . '.pdf');
    }

    /* ----------------------------------------------------------------------
     | ENTREGAS: exportación a Excel (solo admin)
     | ---------------------------------------------------------------------- */

    /**
     * Exporta entregas a Excel. $periodo: semana | mes | rango (usa desde-hasta).
     * Solo para usuarios con visión total (Super Admin / Administrador General).
     */
    public function exportarExcel(Request $request, string $periodo = 'rango')
    {
        if (!$this->veTodo()) {
            abort(403, 'Solo administradores pueden exportar el reporte.');
        }

        [$desde, $hasta] = $this->rangoPeriodo($periodo, $request);

        $q = $this->baseQuery()->enEtapa(EstadoConsumo::ETAPA_ENTREGAS);
        if ($desde) {
            $q->whereDate('fecha_validacion', '>=', $desde);
        }
        if ($hasta) {
            $q->whereDate('fecha_validacion', '<=', $hasta);
        }

        $consumos = $q->orderBy('id_remito')->orderBy('afiliado')->get();
        $nombre = 'entregas_' . $this->obraSocialActiva()->codigo . '_' . $periodo . '_' . now()->format('Ymd_His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OspladEntregasExport($consumos),
            $nombre
        );
    }

    protected function rangoPeriodo(string $periodo, Request $request): array
    {
        switch ($periodo) {
            case 'semana':
                return [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()];
            case 'mes':
                return [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()];
            default: // rango (desde-hasta del filtro)
                return [$request->fecha_desde, $request->fecha_hasta];
        }
    }

    /* ----------------------------------------------------------------------
     | Respuestas JSON
     | ---------------------------------------------------------------------- */

    protected function ok(string $message, array $extra = [])
    {
        return response()->json(array_merge(['success' => true, 'message' => $message], $extra));
    }

    protected function error(string $message, int $code = 500)
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
