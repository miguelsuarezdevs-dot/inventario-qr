<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EscanerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Productos (solo admin)
    Route::get('/productos/crear', [ProductoController::class, 'create'])
        ->name('productos.crear')
        ->middleware('can:crear_productos');

    Route::post('/productos', [ProductoController::class, 'store'])
        ->name('productos.store')
        ->middleware('can:crear_productos');

    // Escáner
    Route::get('/escaner', [EscanerController::class, 'index'])
        ->name('escaner')
        ->middleware('can:escanear_qr');

    Route::post('/escaner/buscar', [EscanerController::class, 'buscar'])
        ->name('escaner.buscar')
        ->middleware('can:escanear_qr');

    Route::post('/escaner/procesar', [EscanerController::class, 'procesar'])
        ->name('escaner.procesar')
        ->middleware('can:escanear_qr');

        Route::get('/escaner/remesa/{remesaId}', [EscanerController::class, 'estadoRemesa'])
        ->middleware('auth')
        ->name('escaner.remesa');
});

require __DIR__ . '/auth.php';