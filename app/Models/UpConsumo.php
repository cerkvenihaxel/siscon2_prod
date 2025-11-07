<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpConsumo extends Model
{
    protected $table = 'up_consumos';

    protected $fillable = [
        'afiliado',
        'apellidos',
        'nombres',
        'modelo_plan',
        'nombre_modelo_plan',
        'codigopostal',
        'localidad',
        'provincia',
        'edad',
        'tipo_afiliado',
        'titofam',
        'fecha_tran',
        'prestacion',
        'tipo_pres',
        'cod_prestacion',
        'cant',
        'desc',
        'status',
        'cargo',
        'impos',
        'impot',
        'imptot',
        'adic',
        'nombre_efector',
        'emisor_app',
        'num_tran',
        'cod_prestador',
        'consultorio_prestador',
        'efector_nombre',
        'consultorio_nombre',
        // Campos de flujo
        'estado_flujo',
        'elegibilidad_id',
        'autorizacion_id',
        'anulacion_id',
        'idtran_elegibilidad',
        'idtran_aprobacion',
        'idaut',
        'fecha_elegibilidad',
        'fecha_aprobacion',
        'fecha_entrega',
        'fecha_anulacion',
        'usuario_elegibilidad',
        'usuario_aprobacion',
        'usuario_entrega',
        'usuario_anulacion',
        'observaciones_flujo',
        'usuario_observaciones',
        'fecha_observaciones',
        // Campo remito
        'remito',
        // Columnas de flujo
        'nro_pedido',
        'nro_transporte'
    ];

    protected $casts = [
        'fecha_tran' => 'datetime',
        'cargo' => 'decimal:2',
        'impos' => 'decimal:2',
        'impot' => 'decimal:2',
        'imptot' => 'decimal:2',
        'adic' => 'decimal:2',
        'fecha_elegibilidad' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_anulacion' => 'datetime',
    ];

    /**
     * Scope para filtrar por afiliado
     */
    public function scopeAfiliado($query, $codigo)
    {
        return $query->where('afiliado', $codigo);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeFechaEntre($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_tran', [$desde, $hasta]);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por estado de flujo
     */
    public function scopeEstadoFlujo($query, $estado)
    {
        return $query->where('estado_flujo', $estado);
    }

    /**
     * Scope para consumos pendientes de procesar
     */
    public function scopePendientes($query)
    {
        return $query->where('estado_flujo', 'pendiente');
    }

    /**
     * Scope para consumos listos para elegibilidad
     */
    public function scopeListosParaElegibilidad($query)
    {
        return $query->whereIn('estado_flujo', ['pendiente', 'elegibilidad_no']);
    }

    /**
     * Scope para consumos listos para aprobación
     */
    public function scopeListosParaAprobacion($query)
    {
        return $query->where('estado_flujo', 'elegibilidad_ok');
    }

    /**
     * Scope para consumos listos para entrega
     */
    public function scopeListosParaEntrega($query)
    {
        return $query->where('estado_flujo', 'aprobado');
    }

    /**
     * Scope para consumos entregados
     */
    public function scopeEntregados($query)
    {
        return $query->where('estado_flujo', 'entregado');
    }

    /**
     * Relación con elegibilidad
     */
    public function elegibilidad()
    {
        return $this->belongsTo(\App\Models\UpElegibilidad::class, 'elegibilidad_id');
    }

    /**
     * Relación con autorización previa
     */
    public function autorizacion()
    {
        return $this->belongsTo(\App\Models\UpAutorizacionPrevia::class, 'autorizacion_id');
    }

    /**
     * Relación con anulación
     */
    public function anulacion()
    {
        return $this->belongsTo(\App\Models\UpAnulacion::class, 'anulacion_id');
    }


    /**
     * Relación con afiliado (si existe en tabla local)
     */
    public function afiliadoUp()
    {
        return $this->belongsTo(\App\Models\AfiliadoConvenioUp::class, 'afiliado', 'codigo_afiliado');
    }

    /**
     * Verificar si puede consultar elegibilidad
     */
    public function puedeConsultarElegibilidad()
    {
        return in_array($this->estado_flujo, ['pendiente', 'elegibilidad_no']);
    }

    /**
     * Verificar si puede ejecutar ELG (alias para compatibilidad)
     */
    public function puedeEjecutarELG()
    {
        return $this->puedeConsultarElegibilidad();
    }

    /**
     * Verificar si puede ejecutar AP
     */
    public function puedeEjecutarAP()
    {
        return $this->estado_flujo === 'elegibilidad_ok';
    }

    /**
     * Verificar si puede aprobar prestación
     */
    public function puedeAprobarPrestacion()
    {
        return $this->puedeEjecutarAP();
    }

    /**
     * Verificar si puede validar entrega (método anterior)
     */
    public function puedeValidarEntrega()
    {
        return $this->estado_flujo === 'aprobado';
    }

    /**
     * Verificar si puede generar validación de entrega (nuevo método)
     */
    public function puedeGenerarValidacionEntrega()
    {
        return $this->estado_flujo === 'aprobado' && !empty($this->idaut);
    }

    /**
     * Verificar si puede anular
     */
    public function puedeAnular()
    {
        return in_array($this->estado_flujo, ['aprobado', 'entregado']);
    }

    /**
     * Verificar si está en proceso (no finalizado)
     */
    public function estaEnProceso()
    {
        return !in_array($this->estado_flujo, ['entregado', 'anulado', 'rechazado']);
    }

    /**
     * Verificar si está finalizado
     */
    public function estaFinalizado()
    {
        return in_array($this->estado_flujo, ['entregado', 'anulado', 'rechazado']);
    }

    /**
     * Accessor para badge de estado de flujo
     */
    public function getEstadoFlujoBadgeAttribute()
    {
        $badges = [
            'pendiente' => 'default',
            'elegibilidad_ok' => 'info',
            'elegibilidad_no' => 'danger',
            'aprobado' => 'success',
            'rechazado' => 'danger',
            'entregado' => 'primary',
            'anulado' => 'warning',
        ];
        $color = $badges[$this->estado_flujo] ?? 'default';
        $texto = strtoupper(str_replace('_', ' ', $this->estado_flujo));
        return "<span class='badge badge-{$color}'>{$texto}</span>";
    }

    /**
     * Accessor para nombre completo del afiliado
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->apellidos . ', ' . $this->nombres);
    }

    /**
     * Accessor para descripción del tipo de prestación
     */
    public function getTipoPrestacionDescripcionAttribute()
    {
        $tipos = [
            'P' => 'Prestación',
            'M' => 'Medicamento',
            'D' => 'Derivación',
        ];
        return $tipos[$this->tipo_pres] ?? $this->tipo_pres;
    }

    /**
     * Accessor para verificar si requiere autorización especial
     */
    public function getRequiereAutorizacionEspecialAttribute()
    {
        // Lógica para determinar si requiere autorización especial
        // Por ejemplo, medicamentos de alto costo, tratamientos especiales, etc.
        return $this->imptot > 10000 || in_array($this->tipo_pres, ['D']);
    }


    /**
     * Calcular estado automático basado en los campos
     */
    public function getEstadoCalculadoAttribute()
    {
        if ($this->estado_flujo === 'anulado') {
            return 'anulado';
        }
        if ($this->fecha_entrega) {
            return 'entregado';
        }
        if ($this->nro_transporte) {
            return 'en_transito';
        }
        if ($this->remito) {
            return 'remitado';
        }
        if ($this->nro_pedido) {
            return 'procesado';
        }
        return 'pendiente';
    }

    /**
     * Scope para filtrar por estado calculado
     */
    public function scopeEstadoCalculado($query, $estado)
    {
        switch ($estado) {
            case 'anulado':
                return $query->where('estado_flujo', 'anulado');
            case 'entregado':
                return $query->whereNotNull('fecha_entrega')->where('estado_flujo', '!=', 'anulado');
            case 'en_transito':
                return $query->whereNotNull('nro_transporte')->whereNull('fecha_entrega')->where('estado_flujo', '!=', 'anulado');
            case 'remitado':
                return $query->whereNotNull('remito')->whereNull('nro_transporte')->whereNull('fecha_entrega')->where('estado_flujo', '!=', 'anulado');
            case 'procesado':
                return $query->whereNotNull('nro_pedido')->whereNull('remito')->whereNull('nro_transporte')->whereNull('fecha_entrega')->where('estado_flujo', '!=', 'anulado');
            case 'pendiente':
                return $query->whereNull('nro_pedido')->whereNull('remito')->whereNull('nro_transporte')->whereNull('fecha_entrega')->where('estado_flujo', '!=', 'anulado');
            default:
                return $query;
        }
    }
    public function getSiguientePaso()
    {
        switch ($this->estado_flujo) {
            case 'pendiente':
            case 'elegibilidad_no':
                return [
                    'accion' => 'consultar_elegibilidad',
                    'descripcion' => 'Consultar Elegibilidad',
                    'icono' => 'fa-user-check',
                    'color' => 'info'
                ];
            
            case 'elegibilidad_ok':
                return [
                    'accion' => 'aprobar_prestacion',
                    'descripcion' => 'Aprobar Prestación',
                    'icono' => 'fa-check-circle',
                    'color' => 'success'
                ];
            
            case 'aprobado':
                return [
                    'accion' => 'generar_validacion_entrega',
                    'descripcion' => 'Generar Validación de Entrega',
                    'icono' => 'fa-clipboard-check',
                    'color' => 'primary'
                ];
            
            default:
                return null;
        }
    }

    /**
     * Método para obtener el progreso del flujo (porcentaje)
     */
    public function getProgresoFlujo()
    {
        $estados = [
            'pendiente' => 0,
            'elegibilidad_no' => 25,
            'elegibilidad_ok' => 33,
            'aprobado' => 66,
            'entregado' => 100,
            'rechazado' => 0,
            'anulado' => 0,
        ];

        return $estados[$this->estado_flujo] ?? 0;
    }

    /**
     * Método estático para obtener estadísticas generales
     */
    public static function estadisticasGenerales($fechaDesde = null, $fechaHasta = null)
    {
        $query = static::query();
        
        if ($fechaDesde) {
            $query->where('fecha_tran', '>=', $fechaDesde);
        }
        
        if ($fechaHasta) {
            $query->where('fecha_tran', '<=', $fechaHasta);
        }
        
        return [
            'total_consumos' => $query->count(),
            'pendientes' => $query->where('estado_flujo', 'pendiente')->count(),
            'elegibilidad_ok' => $query->where('estado_flujo', 'elegibilidad_ok')->count(),
            'aprobados' => $query->where('estado_flujo', 'aprobado')->count(),
            'entregados' => $query->where('estado_flujo', 'entregado')->count(),
            'rechazados' => $query->where('estado_flujo', 'rechazado')->count(),
            'anulados' => $query->where('estado_flujo', 'anulado')->count(),
            'importe_total' => $query->sum('imptot'),
            'importe_entregado' => $query->where('estado_flujo', 'entregado')->sum('imptot'),
        ];
    }

    /**
     * Método para obtener consumos por afiliado con historial
     */
    public static function historialAfiliado($codigoAfiliado, $limite = 50)
    {
        return static::where('afiliado', $codigoAfiliado)
                    ->with(['elegibilidad', 'autorizacion', 'validacionEntrega'])
                    ->orderByDesc('fecha_tran')
                    ->limit($limite)
                    ->get();
    }
}
