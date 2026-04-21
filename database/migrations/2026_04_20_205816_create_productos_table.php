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
            
            // Código QR único
            $table->string('codigo_qr')->unique();
            
            // Tus 12 campos
            $table->string('remesa');
            $table->integer('unidades_iniciales');
            $table->string('destinatario');
            $table->string('sucursal')->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad');
            $table->string('cliente');
            $table->text('observacion')->nullable();
            $table->text('documentos')->nullable();
            $table->string('zona')->nullable();
            $table->string('ruta')->nullable();
            $table->date('elaboracion');
            
            // Campos de inventario
            $table->integer('unidades_actuales');
            $table->string('ubicacion_actual')->default('Bodega Principal');
            $table->enum('estado', ['activo', 'en_transito', 'entregado', 'parcial'])->default('activo');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};