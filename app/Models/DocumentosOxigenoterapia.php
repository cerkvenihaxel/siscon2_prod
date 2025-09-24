<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosOxigenoterapia extends Model
{
    use HasFactory;

    protected $table = 'documentos_oxigenoterapia';

    protected $fillable = [
        'pedido_oxigenoterapia_id',
        'prestamo_oxigenoterapia_id',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
        'mime_type',
        'tamaño_bytes',
        'observaciones',
        'firmado',
        'fecha_firma',
        'firmado_por',
        'stamp_user'
    ];

    protected $casts = [
        'firmado' => 'boolean',
        'fecha_firma' => 'datetime',
        'tamaño_bytes' => 'integer'
    ];

    // Tipos de documentos disponibles
    const TIPOS_DOCUMENTO = [
        'consentimiento' => 'Consentimiento Informado',
        'terminos' => 'Términos y Condiciones',
        'contrato' => 'Contrato de Préstamo',
        'instrucciones' => 'Instrucciones de Uso',
        'checklist' => 'Checklist de Entrega',
        'resumen' => 'Resumen del Pedido'
    ];

    // Relaciones
    public function pedidoOxigenoterapia()
    {
        return $this->belongsTo(PedidoOxigenoterapia::class, 'pedido_oxigenoterapia_id');
    }

    public function prestamoOxigenoterapia()
    {
        return $this->belongsTo(PrestamoOxigenoterapia::class, 'prestamo_oxigenoterapia_id');
    }

    // Métodos de utilidad
    public function getTipoDocumentoLabelAttribute()
    {
        return self::TIPOS_DOCUMENTO[$this->tipo_documento] ?? $this->tipo_documento;
    }

    public function getTamañoFormateadoAttribute()
    {
        if (!$this->tamaño_bytes) {
            return 'N/A';
        }

        $bytes = $this->tamaño_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getUrlDescargaAttribute()
    {
        return route('documentos.descargar', $this->id);
    }

    public function getUrlVerAttribute()
    {
        return route('documentos.ver', $this->id);
    }

    // Scopes
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }

    public function scopeFirmados($query)
    {
        return $query->where('firmado', true);
    }

    public function scopeNoFirmados($query)
    {
        return $query->where('firmado', false);
    }

    public function scopePorPedido($query, $pedidoId)
    {
        return $query->where('pedido_oxigenoterapia_id', $pedidoId);
    }

    public function scopePorPrestamo($query, $prestamoId)
    {
        return $query->where('prestamo_oxigenoterapia_id', $prestamoId);
    }
}
