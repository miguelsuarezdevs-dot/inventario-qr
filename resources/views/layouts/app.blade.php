<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventario QR - @yield('title')</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Navegación */
        .navbar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-links a {
            color: #333;
            text-decoration: none;
            margin-right: 20px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            background: #e9ecef;
        }
        
        .user-info {
            color: #666;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            margin-left: 10px;
        }
        
        /* Tarjetas */
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        
        /* Formulario */
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #007bff;
        }
        
        /* Botones */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-primary {
            background: #28a745;
            color: white;
        }
        
        .btn-primary:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        /* Utilidades */
        .text-center {
            text-align: center;
        }
        
        .text-gray {
            color: #6c757d;
            font-size: 12px;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .navbar {
                flex-direction: column;
                gap: 10px;
            }
            
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="nav-links">
                <a href="{{ route('dashboard') }}">🏠 Inicio</a>
                @can('escanear_qr')
                <a href="{{ route('escaner') }}">📷 Escáner QR</a>
                @endcan
                @can('crear_productos')
                <a href="{{ route('productos.crear') }}">🏷️ Nuevo Sticker</a>
                @endcan
            </div>
            
            <div class="user-info">
                👤 {{ auth()->user()->name }}
                ({{ auth()->user()->roles->first()->name ?? 'sin rol' }})
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">🚪 Cerrar sesión</button>
                </form>
            </div>
        </nav>
        
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>