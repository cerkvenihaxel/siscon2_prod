<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpValidacionEntrega extends Model
{
    protected $table = 'up_validaciones_entrega';
    
    protected $fillable = [
        'consumo_id',
        'afiliado_codigo',
        'afiliado_nombre',
        'afiliado_apellido',
        'medicamento_codigo',
        'medicamento_descripcion',
        'cantidad',
        'importe_autorizado',
        'idaut',
        'fecha_entrega',
        'usuario_entrega',
        'farmacia_codigo',
        'farmacia_nombre',
        'farmacia_direccion',
        'observaciones',
        'numero_receta',
        'medico_prescriptor',
        'entrega_completa',
        'cantidad_entregada',
        'lote_medicamento',
        'fecha_vencimiento',
        'laboratorio',
        'ip_entrega',
        'dispositivo_entrega',
        'fecha_confirmacion',
        'usuario_confirmacion',
    ];
    
    protected $casts = [
        'fecha_entrega' => 'datetime',
        'fecha_vencimiento' => 'date',
        'fecha_confirmacion' => 'datetime',
        'cantidad' => 'integer',
        'cantidad_entregada' => 'integer',
        'importe_autorizado' => 'decimal:2',
        'entrega_completa' => 'boolean',
    ];

    /**
     * Relación con el consumo
     */
    public function consumo(): BelongsTo
    {
        return $this->belongsTo(UpConsumo::class, 'consumo_id');
    }
    
    /**
     * Relación con el afiliado (si existe en la tabla local)
     */
    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(AfiliadoConvenioUp::class, 'afiliado_codigo', 'codigo_afiliado');
    }

    /**
     * Scope para filtrar por afiliado
     */
    public function scopeAfiliado($query, $codigo)
    {
        return $query->where('afiliado_codigo', $codigo);
    }

    /**
     * Scope para filtrar por farmacia
     */
    public function scopeFarmacia($query, $codigo)
    {
        return $query->where('farmacia_codigo', $codigo);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeFechaEntre($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_entrega', [$desde, $hasta]);
    }

    /**
     * Scope para entregas del día
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_entrega', today());
    }

    /**
     * Scope para entregas del mes
     */
    public function scopeMesActual($query)
    {
        return $query->whereMonth('fecha_entrega', now()->month)
                    ->whereYear('fecha_entrega', now()->year);
    }

    /**
     * Accessor para nombre completo del afiliado
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->afiliado_apellido . ', ' . $this->afiliado_nombre);
    }

    /**
     * Accessor para verificar si la entrega está completa
     */
    public function getEsEntregaCompletaAttribute()
    {
        if ($this->entrega_completa) {
            return true;
        }
        
        return $this->cantidad_entregada >= $this->cantidad;
    }

    /**
     * Accessor para cantidad pendiente
     */
    public function getCantidadPendienteAttribute()
    {
        if ($this->entrega_completa) {
            return 0;
        }
        
        return max(0, $this->cantidad - ($this->cantidad_entregada ?? 0));
    }

    /**
     * Método para generar número de comprobante único
     */
    public function generarNumeroComprobante()
    {
        return 'VE-' . $this->id . '-' . $this->fecha_entrega->format('Ymd');
    }

    /**
     * Método para confirmar la entrega
     */
    public function confirmar($usuario = null)
    {
        $this->update([
            'fecha_confirmacion' => now(),
            'usuario_confirmacion' => $usuario ?? auth()->user()->email ?? 'sistema',
        ]);
        
        return $this;
    }

    /**
     * Método estático para crear validación desde consumo
     */
    public static function crearDesdeConsumo(UpConsumo $consumo, array $datosAdicionales = [])
    {
        $datos = array_merge([
            'consumo_id' => $consumo->id,
            'afiliado_codigo' => $consumo->afiliado,
            'afiliado_nombre' => $consumo->nombres,
            'afiliado_apellido' => $consumo->apellidos,
            'medicamento_codigo' => $consumo->cod_prestacion,
            'medicamento_descripcion' => $consumo->desc,
            'cantidad' => $consumo->cant,
            'importe_autorizado' => $consumo->imptot,
            'idaut' => $consumo->idaut,
            'fecha_entrega' => now(),
            'usuario_entrega' => auth()->user()->email ?? 'sistema',
            'ip_entrega' => request()->ip(),
            'dispositivo_entrega' => request()->userAgent(),
        ], $datosAdicionales);
        
        return static::create($datos);
    }

    /**
     * Método para obtener estadísticas de entregas
     */
    public static function estadisticas($fechaDesde = null, $fechaHasta = null)
    {
        $query = static::query();
        
        if ($fechaDesde) {
            $query->where('fecha_entrega', '>=', $fechaDesde);
        }
        
        if ($fechaHasta) {
            $query->where('fecha_entrega', '<=', $fechaHasta);
        }
        
        return [
            'total_entregas' => $query->count(),
            'entregas_completas' => $query->where('entrega_completa', true)->count(),
            'entregas_parciales' => $query->where('entrega_completa', false)->count(),
            'importe_total' => $query->sum('importe_autorizado'),
            'cantidad_total_medicamentos' => $query->sum('cantidad'),
            'farmacias_activas' => $query->distinct('farmacia_codigo')->count('farmacia_codigo'),
            'afiliados_atendidos' => $query->distinct('afiliado_codigo')->count('afiliado_codigo'),
        ];
    }

    /**
     * Método para obtener top farmacias por entregas
     */
    public static function topFarmacias($limite = 10, $fechaDesde = null, $fechaHasta = null)
    {
        $query = static::select('farmacia_codigo', 'farmacia_nombre')
                      ->selectRaw('COUNT(*) as total_entregas')
                      ->selectRaw('SUM(importe_autorizado) as importe_total')
                      ->whereNotNull('farmacia_codigo');
        
        if ($fechaDesde) {
            $query->where('fecha_entrega', '>=', $fechaDesde);
        }
        
        if ($fechaHasta) {
            $query->where('fecha_entrega', '<=', $fechaHasta);
        }
        
        return $query->groupBy('farmacia_codigo', 'farmacia_nombre')
                    ->orderByDesc('total_entregas')
                    ->limit($limite)
                    ->get();
    }

    /**
     * Método para obtener entregas por día (para gráficos)
     */
    public static function entregasPorDia($fechaDesde = null, $fechaHasta = null)
    {
        $query = static::selectRaw('DATE(fecha_entrega) as fecha')
                      ->selectRaw('COUNT(*) as total_entregas')
                      ->selectRaw('SUM(importe_autorizado) as importe_total');
        
        if ($fechaDesde) {
            $query->where('fecha_entrega', '>=', $fechaDesde);
        } else {
            $query->where('fecha_entrega', '>=', now()->subDays(30));
        }
        
        if ($fechaHasta) {
            $query->where('fecha_entrega', '<=', $fechaHasta);
        }
        
        return $query->groupByRaw('DATE(fecha_entrega)')
                    ->orderBy('fecha')
                    ->get();
    }
}
