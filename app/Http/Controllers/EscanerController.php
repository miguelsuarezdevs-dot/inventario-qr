<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class EscanerController extends Controller
{
    // Mostrar página del escáner
    public function index()
    {
        return view('escaner.index');
    }
    
    // Buscar producto por QR (para mostrar info)
    public function buscar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);
        
        $producto = Producto::where('codigo_qr', $request->codigo)->first();
        
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'remesa' => $producto->remesa,
                'cliente' => $producto->cliente,
                'destinatario' => $producto->destinatario,
                'ciudad' => $producto->ciudad,
                'unidades_actuales' => $producto->unidades_actuales,
                'estado' => $producto->estado,
                'ubicacion' => $producto->ubicacion_actual
            ]
        ]);
    }
    
    // Procesar entrada o salida de stock
    public function procesar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'accion' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1'
        ]);
        
        $producto = Producto::find($request->producto_id);
        
        // Validar stock suficiente para salida
        if ($request->accion === 'salida' && $producto->unidades_actuales < $request->cantidad) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuficiente. Actual: {$producto->unidades_actuales} unidades"
            ], 400);
        }
        
        // Actualizar stock
        $cantidadAnterior = $producto->unidades_actuales;
        
        if ($request->accion === 'entrada') {
            $producto->unidades_actuales += $request->cantidad;
            $producto->estado = 'activo';
            $mensaje = "✅ Se agregaron {$request->cantidad} unidades";
        } else {
            $producto->unidades_actuales -= $request->cantidad;
            if ($producto->unidades_actuales === 0) {
                $producto->estado = 'entregado';
            }
            $mensaje = "✅ Se retiraron {$request->cantidad} unidades";
        }
        
        $producto->save();
        
        // Registrar movimiento
        Movimiento::create([
            'producto_id' => $producto->id,
            'user_id' => auth()->id(),
            'tipo' => $request->accion,
            'cantidad' => $request->cantidad,
            'observacion' => "Movimiento por escáner QR. Stock anterior: {$cantidadAnterior}"
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'stock_actual' => $producto->unidades_actuales,
            'estado' => $producto->estado
        ]);
    }
}