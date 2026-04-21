<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $fillable = [
        'producto_id',
        'user_id',
        'tipo',
        'cantidad',
        'observacion'
    ];
    
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}