@extends('layouts.app')

@section('title', 'Sticker QR Generado')

@section('content')
<div class="bg-white rounded-lg shadow p-6 text-center">
    <h1 class="text-2xl font-bold mb-4">✅ Sticker QR Generado</h1>
    
    <div class="border-2 border-gray-300 rounded-lg p-6 inline-block mx-auto">
        <img src="data:image/png;base64,{{ $qrImage }}" class="mx-auto" style="width: 200px;">
        
        <div class="mt-4 text-left border-t pt-4">
            <p><strong>Remesa:</strong> {{ $producto->remesa }}</p>
            <p><strong>Cliente:</strong> {{ $producto->cliente }}</p>
            <p><strong>Destinatario:</strong> {{ $producto->destinatario }}</p>
            <p><strong>Ciudad:</strong> {{ $producto->ciudad }}</p>
            <p><strong>Unidades:</strong> {{ $producto->unidades_iniciales }}</p>
            <p><strong>Fecha:</strong> {{ $producto->elaboracion->format('d/m/Y') }}</p>
        </div>
    </div>
    
    <div class="mt-6 space-x-4">
        <button onclick="window.print()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">
            🖨️ Imprimir Sticker
        </button>
        <a href="{{ route('productos.crear') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg inline-block">
            ➕ Nuevo Producto
        </a>
        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg inline-block">
            🏠 Volver al Inicio
        </a>
    </div>
</div>

<style>
    @media print {
        nav, .mt-6, .bg-white .shadow, button, a {
            display: none;
        }
        .border-2 {
            border: 1px solid black;
            margin: 0;
            padding: 10px;
        }
    }
</style>
@endsection