<?php

namespace App\Models\Drogueria;

use App\Support\ObraSocial\EstadoConsumo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Consumo habilitado de obra social (tabla drogueria.osplad_consumos).
 *
 * Genérico para cualquier obra social: se filtra por os_id. El estado del flujo
 * vive en `estado_pedido` (ver App\Support\ObraSocial\EstadoConsumo).
 *
 * Nota: la tabla NO tiene auto_increment ni timestamps. PK = id_consumo.
 * No contiene columnas de precio/costo (los precios quedan ocultos por diseño).
 */
class ObraSocialConsumo extends Model
{
    protected $connection = 'drogueria';
    protected $table = 'osplad_consumos';
    protected $primaryKey = 'id_consumo';
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'id_consumo'       => 'integer',
        'id_convenio'      => 'integer',
        'os_id'            => 'integer',
        'cantidad'         => 'integer',
        'id_cliente'       => 'integer',
        'sin_stock'        => 'integer',
        'reasignado'       => 'integer',
        'fecha'            => 'date',
        'fecha_remito'     => 'date',
        'fecha_pedido'     => 'datetime',
        'fecha_validacion' => 'datetime',
        'notif_transito_at' => 'datetime',
    ];

    /* ----------------------------------------------------------------------
     | Relaciones
     | ---------------------------------------------------------------------- */

    public function obraSocial()
    {
        return $this->belongsTo(ObraSocial::class, 'os_id');
    }

    /* ----------------------------------------------------------------------
     | Scopes
     | ---------------------------------------------------------------------- */

    /** Filtra por obra social (clave de la escalabilidad multi-OS). */
    public function scopeDeObraSocial(Builder $q, int $osId): Builder
    {
        return $q->where('os_id', $osId);
    }

    /** Filtra por farmacia (osplad_consumos.id_cliente). */
    public function scopeDeFarmacia(Builder $q, $idCliente): Builder
    {
        return $q->where('id_cliente', $idCliente);
    }

    /** Filtra por estado del flujo (uno o varios códigos). */
    public function scopeEnEstado(Builder $q, $estados): Builder
    {
        $estados = (array) $estados;
        return $q->where(function ($sub) use ($estados) {
            $sub->whereIn('estado_pedido', $estados);
            // PEND también cubre filas con estado NULL/'' (datos no migrados)
            if (in_array(EstadoConsumo::PENDIENTE, $estados, true)) {
                $sub->orWhereNull('estado_pedido')->orWhere('estado_pedido', '');
            }
        });
    }

    /** Filtra por la etapa del flujo (pendientes | transito | entregas). */
    public function scopeEnEtapa(Builder $q, string $etapa): Builder
    {
        return $q->enEstado(EstadoConsumo::estadosDeEtapa($etapa));
    }

    /** Consumos En tránsito que aún no recibieron la notificación "procesando". */
    public function scopeSinNotificarTransito(Builder $q): Builder
    {
        return $q->where('estado_pedido', EstadoConsumo::EN_TRANSITO)
                 ->whereNull('notif_transito_at')
                 ->whereNotNull('telefono')
                 ->where('telefono', '!=', '');
    }

    /* ----------------------------------------------------------------------
     | Accessors / helpers de estado
     | ---------------------------------------------------------------------- */

    public function getEstadoCodigoAttribute(): string
    {
        return EstadoConsumo::normalizar($this->estado_pedido);
    }

    public function getEstadoLabelAttribute(): string
    {
        return EstadoConsumo::label($this->estado_pedido);
    }

    public function getEstadoBadgeAttribute(): string
    {
        return EstadoConsumo::badge($this->estado_pedido);
    }

    public function esPendiente(): bool
    {
        return $this->estado_codigo === EstadoConsumo::PENDIENTE;
    }

    public function esEnTransito(): bool
    {
        return $this->estado_codigo === EstadoConsumo::EN_TRANSITO;
    }

    public function esEntregado(): bool
    {
        return $this->estado_codigo === EstadoConsumo::ENTREGADO;
    }

    public function tieneRemito(): bool
    {
        return !empty($this->id_remito);
    }

    /** Nombre de afiliado (ya viene completo en la columna afiliado). */
    public function getNombreAfiliadoAttribute(): string
    {
        return trim((string) $this->attributes['afiliado']);
    }
}
