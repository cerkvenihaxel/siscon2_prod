<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WspPedidosController extends Controller
{
    private const PAGE_SIZE = 25;

    private const ESTADOS = ['CONFIRMADO', 'PROCESANDO', 'ENTREGADO', 'CANCELADO'];

    private const ESTADO_STYLE = [
        'CONFIRMADO' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'dot' => '#42a5f5'],
        'PROCESANDO' => ['bg' => '#fff8e1', 'color' => '#f57f17', 'dot' => '#ffc107'],
        'ENTREGADO'  => ['bg' => '#e8f5e9', 'color' => '#2e7d32', 'dot' => '#66bb6a'],
        'CANCELADO'  => ['bg' => '#fce4ec', 'color' => '#c62828', 'dot' => '#ef9a9a'],
    ];

    private const PAGO_ICON = [
        'mercadopago'   => '💳',
        'transferencia' => '🏦',
        'tarjeta'       => '💳',
        'efectivo'      => '💵',
    ];

    private const ENVIO_ICON = [
        'A_DOMICILIO'  => '🛵',
        'RETIRO_LOCAL' => '🏪',
    ];

    // ── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $page        = max(1, (int)$request->get('page', 1));
        $search      = trim((string)$request->get('q', ''));
        $estado      = trim((string)$request->get('estado', ''));
        $tipoEnvio   = trim((string)$request->get('tipo', ''));
        $metodoPago  = trim((string)$request->get('pago', ''));
        $fecha       = trim((string)$request->get('fecha', ''));

        $query = DB::table('wsp_pedidos')->orderByDesc('pedido_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pedido_id', 'LIKE', "%{$search}%")
                  ->orWhere('cliente_nombre', 'LIKE', "%{$search}%")
                  ->orWhere('cliente_dni', 'LIKE', "%{$search}%")
                  ->orWhere('usuario_id', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
        }
        if ($estado)     $query->where('estado', $estado);
        if ($tipoEnvio)  $query->where('tipo_envio', $tipoEnvio);
        if ($metodoPago) $query->where('metodo_pago', $metodoPago);
        if ($fecha)      $query->whereDate('pedido_at', $fecha);

        $total = $query->count();
        $pages = max(1, (int)ceil($total / self::PAGE_SIZE));
        $rows  = $query->skip(($page - 1) * self::PAGE_SIZE)->take(self::PAGE_SIZE)->get();

        $stats = $this->getStats();

        return view('wsp_pedidos.index', [
            'rows'         => $rows,
            'total'        => $total,
            'pages'        => $pages,
            'page'         => $page,
            'search'       => $search,
            'estado'       => $estado,
            'tipoEnvio'    => $tipoEnvio,
            'metodoPago'   => $metodoPago,
            'fecha'        => $fecha,
            'stats'        => $stats,
            'estados'      => self::ESTADOS,
            'estadoStyle'  => self::ESTADO_STYLE,
            'pagoIcon'     => self::PAGO_ICON,
            'envioIcon'    => self::ENVIO_ICON,
        ]);
    }

    // ── Detalle (AJAX) ────────────────────────────────────────────────────────

    public function show(string $pedidoId)
    {
        $pedido = DB::table('wsp_pedidos')->where('pedido_id', $pedidoId)->first();
        if (!$pedido) abort(404);

        $pedido->medicamentos_arr = json_decode($pedido->medicamentos, true) ?? [];
        $pedido->estado_style     = self::ESTADO_STYLE[$pedido->estado] ?? self::ESTADO_STYLE['CONFIRMADO'];

        return view('wsp_pedidos.detalle', compact('pedido'));
    }

    // ── Actualizar estado ─────────────────────────────────────────────────────

    public function actualizarEstado(Request $request, string $pedidoId)
    {
        $request->validate(['estado' => 'required|in:CONFIRMADO,PROCESANDO,ENTREGADO,CANCELADO']);

        DB::table('wsp_pedidos')
            ->where('pedido_id', $pedidoId)
            ->update([
                'estado'     => $request->estado,
                'updated_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'estado' => $request->estado]);
        }

        return back()->with('success', "Pedido {$pedidoId} actualizado a {$request->estado}");
    }

    // ── Agregar notas ─────────────────────────────────────────────────────────

    public function actualizarNotas(Request $request, string $pedidoId)
    {
        $request->validate(['notas' => 'nullable|string|max:1000']);

        DB::table('wsp_pedidos')
            ->where('pedido_id', $pedidoId)
            ->update(['notas' => $request->notas, 'updated_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    private function getStats(): object
    {
        $base = DB::table('wsp_pedidos');

        return (object)[
            'total'         => $base->count(),
            'total_monto'   => $base->sum('total'),
            'confirmados'   => (clone $base)->where('estado', 'CONFIRMADO')->count(),
            'procesando'    => (clone $base)->where('estado', 'PROCESANDO')->count(),
            'entregados'    => (clone $base)->where('estado', 'ENTREGADO')->count(),
            'cancelados'    => (clone $base)->where('estado', 'CANCELADO')->count(),
            'hoy'           => (clone $base)->whereDate('pedido_at', today())->count(),
            'semana'        => (clone $base)->where('pedido_at', '>=', now()->startOfWeek())->count(),
            'por_metodo'    => DB::table('wsp_pedidos')
                                ->selectRaw('metodo_pago, COUNT(*) as cnt, SUM(total) as monto')
                                ->whereNotNull('metodo_pago')
                                ->groupBy('metodo_pago')
                                ->orderByDesc('cnt')
                                ->get(),
            'por_tipo'      => DB::table('wsp_pedidos')
                                ->selectRaw('tipo_envio, COUNT(*) as cnt')
                                ->whereNotNull('tipo_envio')
                                ->groupBy('tipo_envio')
                                ->get(),
            'ultimos_7dias' => DB::table('wsp_pedidos')
                                ->selectRaw('DATE(pedido_at) as dia, COUNT(*) as cnt, SUM(total) as monto')
                                ->where('pedido_at', '>=', now()->subDays(6)->startOfDay())
                                ->groupBy('dia')
                                ->orderBy('dia')
                                ->get(),
        ];
    }
}
