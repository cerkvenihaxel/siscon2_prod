<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosPrestamo extends Model
{
    use HasFactory;

    protected $table = 'documentos_prestamo';

    protected $fillable = [
        'prestamo_oxigenoterapia_id',
        'tipo_documento',
        'nombre_documento',
        'contenido_documento',
        'archivo_generado',
        'fecha_generacion',
        'fecha_firma',
        'firma_digital',
        'ip_firma',
        'estado_documento',
        'stamp_user'
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'fecha_firma' => 'date',
    ];

    public function prestamoOxigenoterapia()
    {
        return $this->belongsTo(PrestamoOxigenoterapia::class, 'prestamo_oxigenoterapia_id');
    }

    public function isFirmado()
    {
        return $this->estado_documento === 'FIRMADO';
    }

    public function isVencido()
    {
        return $this->estado_documento === 'VENCIDO';
    }

    public function isGenerado()
    {
        return $this->estado_documento === 'GENERADO';
    }
} 