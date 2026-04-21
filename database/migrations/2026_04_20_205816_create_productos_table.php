<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            
            // Código QR único por unidad
            $table->string('codigo_qr')->unique();
            
            // Datos del producto
            $table->string('remesa');           // Solo números
            $table->string('sucursal', 3);      // 3 letras (BAR, SUL, etc)
            $table->string('destinatario');
            $table->text('direccion');
            $table->string('ciudad');
            $table->string('cliente');
            $table->string('documento')->nullable();
            $table->date('fecha');
            
            // Estado del producto (activo, entregado, devuelto)
            $table->enum('estado', ['activo', 'entregado', 'devuelto'])->default('activo');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};