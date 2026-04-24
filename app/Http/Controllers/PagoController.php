<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    // ── API: crear link de pago ───────────────────────────────────────────────

    public function crear(Request $request)
    {
        $request->validate([
            'pedido_id'   => 'required|string|max:100',
            'total'       => 'required|numeric|min:0',
            'descripcion' => 'required|string|max:500',
        ]);

        $pagoId  = Str::random(8);
        $urlPago = url("/pagar/{$pagoId}");

        DB::table('pagos')->insert([
            'pago_id'      => $pagoId,
            'pedido_id'    => $request->pedido_id,
            'total'        => $request->total,
            'descripcion'  => $request->descripcion,
            'estado'       => 'PENDIENTE',
            'callback_url' => $request->get('callback_url', ''),
            'url_pago'     => $urlPago,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'pago_id'   => $pagoId,
            'pedido_id' => $request->pedido_id,
            'total'     => (float)$request->total,
            'estado'    => 'PENDIENTE',
            'url_pago'  => $urlPago,
        ]);
    }

    // ── Página de pago ────────────────────────────────────────────────────────

    public function show(string $pagoId)
    {
        $pago = DB::table('pagos')->where('pago_id', $pagoId)->first();

        if (!$pago) {
            abort(404, 'Link de pago no encontrado o expirado');
        }

        if ($pago->estado !== 'PENDIENTE') {
            return view('pagos.ya_pagado', compact('pago'));
        }

        return view('pagos.pagar', compact('pago'));
    }

    // ── Confirmar pago ────────────────────────────────────────────────────────

    public function confirmar(Request $request, string $pagoId)
    {
        $pago = DB::table('pagos')->where('pago_id', $pagoId)->first();

        if (!$pago || $pago->estado !== 'PENDIENTE') {
            abort(400, 'Pago no disponible');
        }

        $metodo = $request->get('metodo', 'tarjeta');

        DB::table('pagos')->where('pago_id', $pagoId)->update([
            'estado'     => 'APROBADO',
            'metodo'     => $metodo,
            'fecha_pago' => now(),
            'updated_at' => now(),
        ]);

        $pago = DB::table('pagos')->where('pago_id', $pagoId)->first();

        return view('pagos.exito', compact('pago'));
    }

    // ── API: consultar estado ─────────────────────────────────────────────────

    public function estado(string $pagoId)
    {
        $pago = DB::table('pagos')->where('pago_id', $pagoId)->first();

        if (!$pago) {
            return response()->json(['error' => 'no encontrado'], 404);
        }

        return response()->json($pago);
    }
}
