<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// === RUTA DEL DASHBOARD (modificada) ===
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Escáner
    Route::get('/escaner', [App\Http\Controllers\EscanerController::class, 'index'])
        ->name('escaner')
        ->middleware('can:escanear_qr');

    Route::post('/escaner/buscar', [App\Http\Controllers\EscanerController::class, 'buscar'])
        ->middleware('can:escanear_qr');

    Route::post('/escaner/procesar', [App\Http\Controllers\EscanerController::class, 'procesar'])
        ->middleware('can:escanear_qr');
});


// Productos (solo admin puede crear)
Route::get('/productos/crear', [App\Http\Controllers\ProductoController::class, 'create'])
    ->name('productos.crear')
    ->middleware('can:crear_productos');

Route::post('/productos', [App\Http\Controllers\ProductoController::class, 'store'])
    ->name('productos.store')
    ->middleware('can:crear_productos');

Route::get('/productos/buscar/{codigo}', [App\Http\Controllers\ProductoController::class, 'buscarPorQR']);

require __DIR__ . '/auth.php';