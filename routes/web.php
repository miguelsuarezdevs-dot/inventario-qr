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
    
    // Rutas temporales para probar (las mejoraremos después)
    Route::get('/escaner', function () {
        return view('escaner');
    })->name('escaner')->middleware('can:escanear_qr');
    
    Route::get('/productos/crear', function () {
        return view('productos.crear');
    })->name('productos.crear')->middleware('can:crear_productos');
});

require __DIR__.'/auth.php';