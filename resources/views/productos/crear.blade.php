@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Crear Nuevo Producto / Sticker QR</h1>
    
    <form method="POST" action="{{ route('productos.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Remesa *</label>
                <input type="text" name="remesa" required class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Unidades *</label>
                <input type="number" name="unidades_iniciales" required class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Destinatario *</label>
                <input type="text" name="destinatario" required class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Sucursal</label>
                <input type="text" name="sucursal" class="w-full border rounded-lg p-2">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Dirección</label>
                <input type="text" name="direccion" class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Ciudad *</label>
                <input type="text" name="ciudad" required class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Cliente *</label>
                <input type="text" name="cliente" required class="w-full border rounded-lg p-2">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Observación</label>
                <textarea name="observacion" rows="2" class="w-full border rounded-lg p-2"></textarea>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Documentos</label>
                <input type="text" name="documentos" class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Zona</label>
                <input type="text" name="zona" class="w-full border rounded-lg p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Ruta</label>
                <input type="text" name="ruta" class="w-full border rounded-lg p-2">
            </div>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                Generar Sticker QR
            </button>
        </div>
    </form>
</div>
@endsection