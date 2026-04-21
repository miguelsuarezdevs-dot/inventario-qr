@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card">
    <div class="card-header">📊 Panel de Control</div>
    
    <div class="grid">
        @can('escanear_qr')
        <a href="{{ route('escaner') }}" class="btn btn-primary" style="text-align: center;">
            📷 Escanear QR
        </a>
        @endcan
        
        @can('crear_productos')
        <a href="{{ route('productos.crear') }}" class="btn btn-primary" style="text-align: center; background: #17a2b8;">
            🏷️ Generar Sticker
        </a>
        @endcan
    </div>
</div>
@endsection