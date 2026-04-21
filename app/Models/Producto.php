<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'codigo_qr',
        'remesa',
        'unidades_iniciales',
        'unidades_actuales',
        'destinatario',
        'sucursal',
        'direccion',
        'ciudad',
        'cliente',
        'observacion',
        'documentos',
        'zona',
        'ruta',
        'elaboracion',
        'ubicacion_actual',
        'estado'
    ];
    
    protected $casts = [
        'elaboracion' => 'date',
        'unidades_iniciales' => 'integer',
        'unidades_actuales' => 'integer',
    ];
    
    // Relación con movimientos
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
    
    // Generar código QR automáticamente
    public static function generarCodigoQR($datos)
    {
        // Formato: REMESA|CLIENTE|DESTINATARIO|CIUDAD|UNIDADES|FECHA
        return implode('|', [
            $datos['remesa'],
            $datos['cliente'],
            $datos['destinatario'],
            $datos['ciudad'],
            $datos['unidades_iniciales'],
            now()->format('YmdHis')
        ]);
    }
}