@extends('layouts.app')

@section('title', 'Generar Sticker')

@section('content')
<div class="card">
    <div class="card-header">🎫 Generar Sticker(s) por Unidad</div>
    
    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        
        <div class="grid">
            <div class="form-group">
                <label>📦 Remesa * (solo números)</label>
                <input type="text" name="remesa" required 
                       placeholder="Ej: 276028318659"
                       pattern="[0-9]+"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <div class="text-gray">Ejemplo: 276028318659</div>
            </div>
            
            <div class="form-group">
                <label>🏢 Sucursal * (3 letras)</label>
                <input type="text" name="sucursal" required maxlength="3" 
                       placeholder="Ej: BAR"
                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')">
                <div class="text-gray">Ejemplo: BAR, SUL, BOG, MED</div>
            </div>
            
            <div class="form-group">
                <label>🔢 Cantidad de stickers *</label>
                <input type="number" name="cantidad_stickers" required min="1" max="100" value="1">
                <div class="text-gray">Número de unidades a generar (1 sticker por unidad)</div>
            </div>
            
            <div class="form-group">
                <label>👤 Destinatario *</label>
                <input type="text" name="destinatario" required>
            </div>
            
            <div class="form-group">
                <label>📍 Ciudad *</label>
                <input type="text" name="ciudad" required>
            </div>
            
            <div class="form-group">
                <label>🏠 Cliente *</label>
                <input type="text" name="cliente" required>
            </div>
            
            <div class="form-group">
                <label>📄 Documento</label>
                <input type="text" name="documento" placeholder="Ej: 892024-3000035738392">
            </div>
            
            <div class="form-group">
                <label>📅 Fecha *</label>
                <input type="date" name="fecha" required value="{{ date('Y-m-d') }}">
            </div>
        </div>
        
        <div class="form-group">
            <label>📍 Dirección *</label>
            <input type="text" name="direccion" required>
        </div>
        
        <div class="text-center" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">🎫 Generar Sticker(s)</button>
        </div>
    </form>
</div>
@endsection