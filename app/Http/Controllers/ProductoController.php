<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function create()
    {
        return view('productos.crear');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'remesa' => 'required|numeric',
            'sucursal' => 'required|string|size:3|regex:/^[A-Z]+$/',
            'cantidad_stickers' => 'required|integer|min:1|max:100',
            'destinatario' => 'required|string|max:200',
            'direccion' => 'required|string',
            'ciudad' => 'required|string|max:100',
            'cliente' => 'required|string|max:200',
            'documento' => 'nullable|string',
            'fecha' => 'required|date',
        ]);
        
        $cantidad = $request->cantidad_stickers;
        // Crear un ID único para esta remesa (usando remesa + timestamp)
        $remesaId = $request->remesa . '_' . time();
        $productosCreados = [];
        
        for ($i = 1; $i <= $cantidad; $i++) {
            // Generar código QR único por cada unidad
            $codigoQR = implode('|', [
                $request->remesa,
                $request->sucursal,
                $request->cliente,
                $request->fecha,
                $remesaId,
                $i,
                $cantidad
            ]);
            
            // Crear producto
            $producto = Producto::create([
                'codigo_qr' => $codigoQR,
                'remesa' => $request->remesa,
                'sucursal' => strtoupper($request->sucursal),
                'destinatario' => $request->destinatario,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'cliente' => $request->cliente,
                'documento' => $request->documento,
                'fecha' => $request->fecha,
                'estado' => 'activo',
                'numero_unidad' => $i,
                'total_unidades' => $cantidad,
                'remesa_id' => $remesaId,
            ]);
            
            // Registrar movimiento
            Movimiento::create([
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'tipo' => 'creacion',
                'cantidad' => 1,
                'observacion' => "Sticker QR - Unidad {$i} de {$cantidad}"
            ]);
            
            $productosCreados[] = $producto;
        }
        
        if ($cantidad == 1) {
            $qrImage = base64_encode(file_get_contents("https://quickchart.io/qr?text=" . urlencode($productosCreados[0]->codigo_qr) . "&size=150"));
            return view('productos.sticker', compact('productosCreados', 'qrImage', 'cantidad'));
        }
        
        return view('productos.stickers-multiples', compact('productosCreados', 'cantidad'));
    }
}