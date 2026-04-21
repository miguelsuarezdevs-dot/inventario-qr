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

        $partes = explode('|', $request->codigo);

        if (count($partes) < 7) {
            return response()->json([
                'success' => false,
                'message' => 'QR inválido'
            ], 400);
        }

        $remesaId = $partes[4];
        $numeroUnidad = (int) $partes[5];

        $producto = Producto::where('remesa_id', $remesaId)
            ->where('numero_unidad', $numeroUnidad)
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // 🚫 Evitar doble escaneo
        if ($producto->estado === 'entregado') {
            return response()->json([
                'success' => false,
                'message' => "⚠️ Unidad {$producto->numero_unidad} ya fue escaneada"
            ], 400);
        }

        // ✅ Marcar automáticamente
        $producto->estado = 'entregado';
        $producto->save();

        Movimiento::create([
            'producto_id' => $producto->id,
            'user_id' => auth()->id(),
            'tipo' => 'entregado',
            'cantidad' => 1,
            'observacion' => "Auto-scan unidad {$producto->numero_unidad}"
        ]);

        $unidadesRemesa = Producto::where('remesa_id', $remesaId)->get();

        $escaneadas = $unidadesRemesa->where('estado', 'entregado')->count();
        $pendientes = $unidadesRemesa->where('estado', 'activo')->count();

        $faltantes = $unidadesRemesa
            ->where('estado', 'activo')
            ->pluck('numero_unidad')
            ->values();

        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'remesa' => $producto->remesa,
                'sucursal' => $producto->sucursal,
                'destinatario' => $producto->destinatario,
                'ciudad' => $producto->ciudad,
                'cliente' => $producto->cliente,
                'numero_unidad' => $producto->numero_unidad,
                'total_unidades' => $producto->total_unidades,
                'estado' => $producto->estado,
                'escaneadas' => $escaneadas,
                'pendientes' => $pendientes,
                'faltantes' => $faltantes,
                'remesa_id' => $producto->remesa_id
            ]
        ]);
    }

    public function estadoRemesa($remesaId)
    {
        $unidades = Producto::where('remesa_id', $remesaId)
            ->orderBy('numero_unidad')
            ->get();

        if ($unidades->isEmpty()) {
            return response()->json(['success' => false], 404);
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
}
