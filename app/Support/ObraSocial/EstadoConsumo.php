<?php

namespace App\Support\ObraSocial;

/**
 * Máquina de estados del flujo de consumos de obra social (OSPLAD y futuras).
 *
 * El estado vive en osplad_consumos.estado_pedido (varchar 6). Esta clase centraliza
 * los códigos, etiquetas, colores de "pill" (badge AdminLTE/Bootstrap3) y transiciones,
 * para que las vistas y controllers no dupliquen lógica.
 */
class EstadoConsumo
{
    public const PENDIENTE   = 'PEND';
    public const EN_TRANSITO = 'EM';
    public const ENTREGADO   = 'ENT';
    public const ANULADO     = 'ANUL';

    /**
     * Etapas del flujo (las 3 vistas/menús).
     */
    public const ETAPA_PENDIENTES = 'pendientes';
    public const ETAPA_TRANSITO   = 'transito';
    public const ETAPA_ENTREGAS   = 'entregas';

    /**
     * Metadatos por estado: etiqueta visible y color de badge.
     */
    private const META = [
        self::PENDIENTE   => ['label' => 'Pendiente',   'color' => 'warning', 'icon' => 'fa-clock-o'],
        self::EN_TRANSITO => ['label' => 'En tránsito', 'color' => 'info',    'icon' => 'fa-truck'],
        self::ENTREGADO   => ['label' => 'Entregado',   'color' => 'success', 'icon' => 'fa-check-circle'],
        self::ANULADO     => ['label' => 'Anulado',     'color' => 'danger',  'icon' => 'fa-ban'],
    ];

    /**
     * Estados que corresponden a cada etapa del flujo.
     */
    public static function estadosDeEtapa(string $etapa): array
    {
        switch ($etapa) {
            case self::ETAPA_PENDIENTES: return [self::PENDIENTE];
            case self::ETAPA_TRANSITO:   return [self::EN_TRANSITO];
            case self::ETAPA_ENTREGAS:   return [self::ENTREGADO];
            default:                     return [];
        }
    }

    public static function label(?string $estado): string
    {
        $estado = self::normalizar($estado);
        return self::META[$estado]['label'] ?? ucfirst(strtolower((string) $estado));
    }

    public static function color(?string $estado): string
    {
        $estado = self::normalizar($estado);
        return self::META[$estado]['color'] ?? 'default';
    }

    public static function icon(?string $estado): string
    {
        $estado = self::normalizar($estado);
        return self::META[$estado]['icon'] ?? 'fa-circle-o';
    }

    /**
     * HTML del badge (pill) para usar en tablas, igual estilo que el flujo UP.
     */
    public static function badge(?string $estado): string
    {
        $estado = self::normalizar($estado);
        $color  = self::color($estado);
        $label  = self::label($estado);
        $icon   = self::icon($estado);
        return "<span class=\"badge badge-{$color} label-{$color}\"><i class=\"fa {$icon}\"></i> {$label}</span>";
    }

    /**
     * Estado por defecto cuando viene NULL/vacío.
     */
    public static function normalizar(?string $estado): string
    {
        $estado = trim((string) $estado);
        return $estado === '' ? self::PENDIENTE : strtoupper($estado);
    }

    public static function todos(): array
    {
        return array_keys(self::META);
    }
}
