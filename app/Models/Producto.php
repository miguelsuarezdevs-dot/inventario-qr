<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'codigo_qr',
        'remesa',
        'sucursal',
        'destinatario',
        'direccion',
        'ciudad',
        'cliente',
        'documento',
        'fecha',
        'estado',
        'numero_unidad',
        'total_unidades', // 👈 ESTA LÍNEA ES CLAVE
        'remesa_id',
    ];
    
    
    protected $casts = [
        'fecha' => 'date',
    ];
    
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}