@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Panel de Control</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Botón Escáner (visible para operador y admin) --}}
        @can('escanear_qr')
        <a href="{{ route('escaner') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-6 text-center transition">
            <div class="text-4xl mb-2">📷</div>
            <div class="font-bold text-lg">Escanear QR</div>
            <div class="text-sm mt-2">Registrar entradas y salidas</div>
        </a>
        @endcan
        
        {{-- Botón Crear Producto (solo admin) --}}
        @can('crear_productos')
        <a href="{{ route('productos.crear') }}" 
           class="bg-green-500 hover:bg-green-600 text-white rounded-lg p-6 text-center transition">
            <div class="text-4xl mb-2">🏷️</div>
            <div class="font-bold text-lg">Nuevo Producto</div>
            <div class="text-sm mt-2">Generar sticker QR</div>
        </a>
        @endcan
        
        {{-- Botón Reportes (admin y supervisor) --}}
        @can('ver_reportes')
        <a href="#" 
           class="bg-purple-500 hover:bg-purple-600 text-white rounded-lg p-6 text-center transition">
            <div class="text-4xl mb-2">📊</div>
            <div class="font-bold text-lg">Reportes</div>
            <div class="text-sm mt-2">Ver inventario y movimientos</div>
        </a>
        @endcan
        
    </div>
</div>
@endsection