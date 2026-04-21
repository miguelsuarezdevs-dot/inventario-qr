<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductoController extends Controller
{
    // Mostrar formulario de creación
    public function create()
    {
        return view('productos.crear');
    }
    
    // Guardar producto y generar QR
    public function store(Request $request)
    {
        // Validar datos
        $request->validate([
            'remesa' => 'required|string|max:100',
            'unidades_iniciales' => 'required|integer|min:1',
            'destinatario' => 'required|string|max:200',
            'sucursal' => 'nullable|string|max:100',
            'direccion' => 'nullable|string',
            'ciudad' => 'required|string|max:100',
            'cliente' => 'required|string|max:200',
            'observacion' => 'nullable|string',
            'documentos' => 'nullable|string',
            'zona' => 'nullable|string|max:100',
            'ruta' => 'nullable|string|max:100',
        ]);
        
        // Generar código QR
        $codigoQR = Producto::generarCodigoQR($request->all());
        
        // Crear producto
        $producto = Producto::create([
            'codigo_qr' => $codigoQR,
            'remesa' => $request->remesa,
            'unidades_iniciales' => $request->unidades_iniciales,
            'unidades_actuales' => $request->unidades_iniciales,
            'destinatario' => $request->destinatario,
            'sucursal' => $request->sucursal,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'cliente' => $request->cliente,
            'observacion' => $request->observacion,
            'documentos' => $request->documentos,
            'zona' => $request->zona,
            'ruta' => $request->ruta,
            'elaboracion' => now(),
            'ubicacion_actual' => 'Bodega Principal',
            'estado' => 'activo',
        ]);
        
        // Registrar movimiento de creación
        Movimiento::create([
            'producto_id' => $producto->id,
            'user_id' => auth()->id(),
            'tipo' => 'creacion',
            'cantidad' => $request->unidades_iniciales,
            'observacion' => 'Producto creado con sticker QR'
        ]);
        
        // Generar imagen QR
        // $qrImage = base64_encode(QrCode::format('png')->size(200)->generate($codigoQR));
        $qrImage = base64_encode(file_get_contents("https://quickchart.io/qr?text=" . urlencode($codigoQR) . "&size=200"));
        // Mostrar vista con el sticker para imprimir
        return view('productos.sticker', compact('producto', 'qrImage'));
    }
    
    // Buscar producto por código QR (para el escáner)
    public function buscarPorQR($codigo)
    {
        $producto = Producto::where('codigo_qr', $codigo)->first();
        
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        
        return response()->json([
            'id' => $producto->id,
            'remesa' => $producto->remesa,
            'cliente' => $producto->cliente,
            'destinatario' => $producto->destinatario,
            'ciudad' => $producto->ciudad,
            'unidades_actuales' => $producto->unidades_actuales,
            'estado' => $producto->estado,
            'ubicacion' => $producto->ubicacion_actual
        ]);
    }
}