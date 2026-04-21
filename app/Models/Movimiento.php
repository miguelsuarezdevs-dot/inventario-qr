<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    protected $fillable = [
        'producto_id',
        'user_id',
        'tipo',
        'cantidad',
        'observacion'
    ];
    
    protected $casts = [
        'cantidad' => 'integer',
    ];
    
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}