<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker - {{ $productosCreados[0]->remesa }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .sticker {
            width: 550px;
            background: white;
            border: 2px solid #000;
            padding: 15px;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.2);
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            font-weight: bold;
        }
        .remesa-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding: 8px 0;
            margin-bottom: 12px;
            font-weight: bold;
        }
        .info-section {
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .info-section p { margin: 5px 0; font-size: 12px; }
        .cliente-section {
            border-bottom: 1px solid #000;
            padding: 8px 0;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .documento-section {
            border-bottom: 1px solid #000;
            padding: 8px 0;
            margin-bottom: 10px;
        }
        .qr-container { text-align: center; margin: 15px 0; }
        .qr-container img { width: 130px; height: 130px; border: 1px solid #ccc; padding: 5px; }
        .orden-section {
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            border-top: 1px solid #000;
        }
        .buttons { text-align: center; margin-top: 20px; }
        .btn { padding: 10px 20px; margin: 0 5px; border: none; cursor: pointer; border-radius: 5px; }
        .btn-print { background: #28a745; color: white; }
        .btn-back { background: #007bff; color: white; }
        @media print {
            body { background: white; padding: 0; }
            .buttons { display: none; }
            .sticker { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div>
        <div class="sticker">
            <div class="header">
                <span>Sucursal: {{ $productosCreados[0]->sucursal }}</span>
                <span>Fecha: {{ \Carbon\Carbon::parse($productosCreados[0]->fecha)->format('d/m/Y') }}</span>
            </div>
            <div class="remesa-row">
                <span>Remesa: {{ $productosCreados[0]->remesa }}</span>
                <span>Unidades: 1 / 1</span>
            </div>
            <div class="info-section">
                <p><strong>Destinatario:</strong> {{ $productosCreados[0]->destinatario }}</p>
                <p><strong>Dirección:</strong> {{ $productosCreados[0]->direccion }}</p>
                <p><strong>Ciudad:</strong> {{ $productosCreados[0]->ciudad }}</p>
            </div>
            <div class="cliente-section">
                <strong>Cliente:</strong> {{ $productosCreados[0]->cliente }}
            </div>
            <div class="documento-section">
                <strong>Documento:</strong> {{ $productosCreados[0]->documento ?? 'N/A' }}
            </div>
            <div class="qr-container">
                <img src="data:image/png;base64,{{ $qrImage }}" alt="Código QR">
            </div>
            <div class="orden-section">
                Orden de Compra
            </div>
        </div>
        <div class="buttons">
            <button class="btn btn-print" onclick="window.print()">🖨️ Imprimir Sticker</button>
            <button class="btn btn-back" onclick="window.location.href='{{ route('productos.crear') }}'">➕ Nuevo Sticker</button>
        </div>
    </div>
</body>
</html>