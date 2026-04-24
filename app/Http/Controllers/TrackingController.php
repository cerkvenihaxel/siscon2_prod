<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    private const PAGE_SIZE = 20;

    private const TRANSPORT_COLORS = [
        'OCA'              => ['bg' => '#fff3e0', 'color' => '#e65100', 'dot' => '#f57c00'],
        'GLOBAL MEDICA'    => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'dot' => '#0099cc'],
        'MD CARGA'         => ['bg' => '#f3e5f5', 'color' => '#6a1b9a', 'dot' => '#8e24aa'],
        'BUSPACK'          => ['bg' => '#e8f5e9', 'color' => '#2e7d32', 'dot' => '#43a047'],
        'SOCASA S.A'       => ['bg' => '#e0f2f1', 'color' => '#00695c', 'dot' => '#00897b'],
        'JET PACK'         => ['bg' => '#fce4ec', 'color' => '#c62828', 'dot' => '#e53935'],
        'SERVIRRAP'        => ['bg' => '#e8eaf6', 'color' => '#283593', 'dot' => '#3949ab'],
        'ANDESMAR EXPRESS' => ['bg' => '#fff8e1', 'color' => '#f57f17', 'dot' => '#fbc02d'],
    ];

    private const DEFAULT_COLOR = ['bg' => '#f5f5f5', 'color' => '#555', 'dot' => '#999'];

    // ── Connection ───────────────────────────────────────────────────────────

    private function db(): \Illuminate\Database\Connection
    {
        return DB::connection('drogueria');
    }

    private function dbReachable(): bool
    {
        try {
            $this->db()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // ── Queries ──────────────────────────────────────────────────────────────

    private function buildWhere(string $search, string $transporte, string $operador, string $mes): array
    {
        $parts  = [];
        $params = [];

        if ($search) {
            $parts[]  = '(e.nro_guia LIKE ? OR le.id_remito LIKE ? OR e.id_envio = ?)';
            $like     = "%{$search}%";
            $params   = array_merge($params, [$like, $like, is_numeric($search) ? (int)$search : -1]);
        }
        if ($transporte) {
            $parts[]  = 't.descripcion = ?';
            $params[] = $transporte;
        }
        if ($operador) {
            $parts[]  = 'UPPER(e.id_usuario) = ?';
            $params[] = strtoupper($operador);
        }
        if ($mes) {
            $parts[]  = "DATE_FORMAT(e.fecha_guia, '%Y-%m') = ?";
            $params[] = $mes;
        }

        $where = $parts ? ('WHERE ' . implode(' AND ', $parts)) : '';
        return [$where, $params];
    }

    private function getEnvios(int $page, string $search, string $transporte, string $operador, string $mes): array
    {
        $offset = ($page - 1) * self::PAGE_SIZE;
        [$where, $params] = $this->buildWhere($search, $transporte, $operador, $mes);

        $countSql = "
            SELECT COUNT(DISTINCT e.id_envio) AS total
            FROM envios_global_medica e
            INNER JOIN transportes t              ON t.id_transporte = e.id_transporte
            INNER JOIN lin_envio_global_medica le ON le.id_envio     = e.id_envio
            {$where}
        ";
        $totalRow = $this->db()->selectOne($countSql, $params);
        $total    = $totalRow ? (int)$totalRow->total : 0;

        $listSql = "
            SELECT
                e.id_envio,
                t.descripcion                           AS transporte,
                e.nro_guia,
                e.fecha_guia,
                e.bultos,
                t.precio_bulto,
                e.id_usuario,
                e.temporal,
                COUNT(le.id_remito)                     AS cant_remitos,
                MIN(le.id_remito)                       AS primer_remito,
                MAX(le.id_remito)                       AS ultimo_remito,
                DATEDIFF(CURDATE(), e.fecha_guia)       AS dias_transcurridos,
                COALESCE(e.bultos, 0) * t.precio_bulto  AS costo_total
            FROM envios_global_medica e
            INNER JOIN transportes t              ON t.id_transporte = e.id_transporte
            INNER JOIN lin_envio_global_medica le ON le.id_envio     = e.id_envio
            {$where}
            GROUP BY e.id_envio
            ORDER BY e.id_envio DESC
            LIMIT ? OFFSET ?
        ";
        $rows  = $this->db()->select($listSql, array_merge($params, [self::PAGE_SIZE, $offset]));
        $pages = max(1, (int)ceil($total / self::PAGE_SIZE));

        return [$rows, $total, $pages];
    }

    private function getRemitos(int $idEnvio): array
    {
        return $this->db()->select(
            'SELECT id_remito FROM lin_envio_global_medica WHERE id_envio = ? ORDER BY id_remito',
            [$idEnvio]
        );
    }

    private function getDashboardStats(): array
    {
        $conn = $this->db();

        $totals = $conn->selectOne("
            SELECT
                COUNT(DISTINCT e.id_envio)    AS total_envios,
                COUNT(le.id_remito)            AS total_remitos,
                COALESCE(SUM(e.bultos), 0)     AS total_bultos,
                SUM(e.nro_guia IS NULL OR e.nro_guia = '') AS sin_guia,
                MAX(e.fecha_guia)              AS ultimo_despacho
            FROM envios_global_medica e
            INNER JOIN lin_envio_global_medica le ON le.id_envio = e.id_envio
        ");

        $porTransporte = $conn->select("
            SELECT t.descripcion, COUNT(DISTINCT e.id_envio) AS envios,
                   COUNT(le.id_remito) AS remitos, COALESCE(SUM(e.bultos),0) AS bultos
            FROM envios_global_medica e
            INNER JOIN transportes t              ON t.id_transporte = e.id_transporte
            INNER JOIN lin_envio_global_medica le ON le.id_envio     = e.id_envio
            GROUP BY t.id_transporte ORDER BY envios DESC
        ");

        $porMes = $conn->select("
            SELECT DATE_FORMAT(fecha_guia,'%Y-%m') AS mes,
                   COUNT(*) AS envios, COALESCE(SUM(bultos),0) AS bultos
            FROM envios_global_medica
            WHERE fecha_guia IS NOT NULL
            GROUP BY mes ORDER BY mes DESC LIMIT 6
        ");

        $operadores = $conn->select("
            SELECT UPPER(id_usuario) AS op, COUNT(*) AS envios
            FROM envios_global_medica
            WHERE id_usuario IS NOT NULL AND id_usuario != ''
            GROUP BY UPPER(id_usuario) ORDER BY envios DESC
        ");

        return [$totals, $porTransporte, $porMes, $operadores];
    }

    private function getFilterOptions(): array
    {
        $conn = $this->db();

        $transportes = array_column(
            $conn->select('SELECT DISTINCT descripcion FROM transportes ORDER BY descripcion'),
            'descripcion'
        );
        $operadores = array_column(
            $conn->select("SELECT DISTINCT UPPER(id_usuario) AS op FROM envios_global_medica WHERE id_usuario IS NOT NULL AND id_usuario != '' ORDER BY op"),
            'op'
        );
        $meses = array_column(
            $conn->select("SELECT DISTINCT DATE_FORMAT(fecha_guia,'%Y-%m') AS mes FROM envios_global_medica WHERE fecha_guia IS NOT NULL ORDER BY mes DESC LIMIT 12"),
            'mes'
        );

        return [$transportes, $operadores, $meses];
    }

    // ── Actions ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $page       = max(1, (int)$request->get('page', 1));
        $search     = trim((string)$request->get('q', ''));
        $transporte = trim((string)$request->get('t', ''));
        $operador   = trim((string)$request->get('op', ''));
        $mes        = trim((string)$request->get('mes', ''));

        $dbOnline = $this->dbReachable();

        try {
            [$transportesList, $operadoresList, $mesesList] = $this->getFilterOptions();
            [$totals, $porTransporte, $porMes, $opStats]    = $this->getDashboardStats();
            [$rows, $total, $pages] = $this->getEnvios($page, $search, $transporte, $operador, $mes);

            // Adjuntar remitos a cada envío (solo si tiene ≤50)
            foreach ($rows as $row) {
                $row->remitos = $row->cant_remitos <= 50 ? $this->getRemitos((int)$row->id_envio) : [];
            }

            $error = null;
        } catch (\Exception $e) {
            $rows = $transportesList = $operadoresList = $mesesList = [];
            $totals = $porTransporte = $porMes = $opStats = null;
            $total  = $pages = 0;
            $error  = $e->getMessage();
        }

        return view('tracking.index', compact(
            'rows', 'total', 'pages', 'page',
            'search', 'transporte', 'operador', 'mes',
            'transportesList', 'operadoresList', 'mesesList',
            'totals', 'porTransporte', 'porMes', 'opStats',
            'dbOnline', 'error'
        ));
    }

    public function apiEnvios(Request $request)
    {
        try {
            [$rows, $total, $pages] = $this->getEnvios(
                max(1, (int)$request->get('page', 1)),
                trim((string)$request->get('q', '')),
                trim((string)$request->get('t', '')),
                trim((string)$request->get('op', '')),
                trim((string)$request->get('mes', ''))
            );
            return response()->json(['rows' => $rows, 'total' => $total, 'pages' => $pages]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── Helpers (used in blade) ───────────────────────────────────────────────

    public static function transportColor(string $transporte): array
    {
        return self::TRANSPORT_COLORS[$transporte] ?? self::DEFAULT_COLOR;
    }

    public static function calcularEstado(?string $nroGuia, $dias): array
    {
        if (!$nroGuia) {
            return ['key' => 'prep',      'label' => 'En preparación', 'icon' => '🔧', 'color' => '#e65100', 'bg' => '#fff3e0', 'step' => 1];
        }
        if ($dias === null) {
            return ['key' => 'transit',   'label' => 'En tránsito',    'icon' => '🚚', 'color' => '#1565c0', 'bg' => '#e3f2fd', 'step' => 3];
        }
        $d = (int)$dias;
        if ($d > 7) {
            return ['key' => 'delivered', 'label' => 'Entregado',      'icon' => '✅', 'color' => '#2e7d32', 'bg' => '#e8f5e9', 'step' => 4];
        }
        return     ['key' => 'transit',   'label' => 'En tránsito',    'icon' => '🚚', 'color' => '#1565c0', 'bg' => '#e3f2fd', 'step' => 3];
    }
}
