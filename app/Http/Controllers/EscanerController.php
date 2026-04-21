<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class EscanerController extends Controller
{
    public function index()
    {
        return view('escaner.index');
    }

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

        // Obtener todas las unidades de la misma remesa
        $unidadesRemesa = Producto::where('remesa_id', $producto->remesa_id)
            ->orderBy('numero_unidad')
            ->get();

        // Contar escaneadas vs pendientes
        $escaneadas = $unidadesRemesa->where('estado', 'entregado')->count();
        $pendientes = $unidadesRemesa->where('estado', 'activo')->count();

        // Lista de unidades faltantes
        $faltantes = [];
        foreach ($unidadesRemesa as $unidad) {
            if ($unidad->estado === 'activo') {
                $faltantes[] = $unidad->numero_unidad;
            }
        }

        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'remesa' => $producto->remesa,
                'sucursal' => $producto->sucursal,
                'destinatario' => $producto->destinatario,
                'direccion' => $producto->direccion,
                'ciudad' => $producto->ciudad,
                'cliente' => $producto->cliente,
                'documento' => $producto->documento,
                'fecha' => $producto->fecha->format('d/m/Y'),
                'estado' => $producto->estado,
                'numero_unidad' => $producto->numero_unidad,
                'total_unidades' => $producto->total_unidades,
                'escaneadas' => $escaneadas,
                'pendientes' => $pendientes,
                'faltantes' => $faltantes
            ]
        ]);
    }

    public function estadoRemesa($remesaId)
    {
        $unidades = Producto::where('remesa_id', $remesaId)
            ->orderBy('numero_unidad')
            ->get();

        if ($unidades->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Remesa no encontrada'], 404);
        }

        $primera = $unidades->first();

        return response()->json([
            'success' => true,
            'remesa_id' => $remesaId,
            'remesa' => $primera->remesa,
            'sucursal' => $primera->sucursal,
            'destinatario' => $primera->destinatario,
            'ciudad' => $primera->ciudad,
            'total_unidades' => $unidades->count(),
            'unidades' => $unidades->map(function ($u) {
                return [
                    'numero_unidad' => $u->numero_unidad,
                    'estado' => $u->estado
                ];
            })
        ]);
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'accion' => 'required|in:entregado,devuelto'
        ]);

        $producto = Producto::find($request->producto_id);

        if ($request->accion === 'entregado' && $producto->estado === 'entregado') {
            return response()->json([
                'success' => false,
                'message' => '❌ Esta unidad ya fue entregada anteriormente'
            ], 400);
        }

        if ($request->accion === 'devuelto' && $producto->estado === 'devuelto') {
            return response()->json([
                'success' => false,
                'message' => '❌ Esta unidad ya fue devuelta anteriormente'
            ], 400);
        }

        $producto->estado = $request->accion;
        $producto->save();

        Movimiento::create([
            'producto_id' => $producto->id,
            'user_id' => auth()->id(),
            'tipo' => $request->accion,
            'cantidad' => 1,
            'observacion' => "Unidad {$producto->numero_unidad} de {$producto->total_unidades}"
        ]);

        // Obtener estadísticas actualizadas
        $unidadesRemesa = Producto::where('remesa_id', $producto->remesa_id)->get();
        $escaneadas = $unidadesRemesa->where('estado', 'entregado')->count();
        $pendientes = $unidadesRemesa->where('estado', 'activo')->count();

        $mensaje = $request->accion === 'entregado'
            ? "✅ Unidad {$producto->numero_unidad} de {$producto->total_unidades} entregada"
            : "✅ Unidad {$producto->numero_unidad} de {$producto->total_unidades} devuelta";

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'estado' => $producto->estado,
            'numero_unidad' => $producto->numero_unidad,
            'total_unidades' => $producto->total_unidades,
            'escaneadas' => $escaneadas,
            'pendientes' => $pendientes
        ]);
    }
}