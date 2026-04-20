<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventario QR - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    {{-- Barra de navegación --}}
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                {{-- Logo --}}
                <div class="text-xl font-bold text-blue-600">
                    📦 Inventario QR
                </div>
                
                {{-- Menú --}}
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-600">
                            {{ auth()->user()->name }} 
                            <span class="text-sm text-gray-400">({{ auth()->user()->roles->first()->name ?? 'sin rol' }})</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                Cerrar sesión
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    {{-- Scripts --}}
    @stack('scripts')
</body>
</html>