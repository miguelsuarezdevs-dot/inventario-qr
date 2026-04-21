<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cantidad }} Stickers generados</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .sticker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(550px, 1fr));
            gap: 20px;
        }
        
        .sticker {
            background: white;
            border: 2px solid #000;
            padding: 15px;
            box-shadow: 3px 3px 10px rgba(0,0,0,0.1);
            break-inside: avoid;
            page-break-inside: avoid;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 13px;
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
        
        .info-section p {
            margin: 5px 0;
            font-size: 12px;
        }
        
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
        
        .qr-container {
            text-align: center;
            margin: 15px 0;
        }
        
        .qr-container img {
            width: 120px;
            height: 120px;
            border: 1px solid #ccc;
            padding: 5px;
        }
        
        .orden-section {
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            margin-top: 5px;
        }
        
        .unidad-numero {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .buttons {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        
        .btn-print {
            background: #28a745;
            color: white;
        }
        
        .btn-back {
            background: #007bff;
            color: white;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .buttons {
                display: none;
            }
            .sticker {
                box-shadow: none;
                border: 1px solid #000;
                page-break-after: always;
                break-inside: avoid;
            }
            .sticker-grid {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="buttons">
            <button class="btn btn-print" onclick="window.print()">🖨️ Imprimir todos los stickers</button>
            <button class="btn btn-back" onclick="window.location.href='{{ route('productos.crear') }}'">➕ Generar más stickers</button>
            <button class="btn btn-back" onclick="window.location.href='{{ route('dashboard') }}'">🏠 Volver al inicio</button>
        </div>
        
        <div class="sticker-grid">
            @foreach($productosCreados as $index => $producto)
            <?php 
                $qrImage = base64_encode(file_get_contents("https://quickchart.io/qr?text=" . urlencode($producto->codigo_qr) . "&size=120"));
            ?>
            <div class="sticker">
                <div class="header">
                    <span>Sucursal: {{ $producto->sucursal }}</span>
                    <span>Fecha: {{ \Carbon\Carbon::parse($producto->fecha)->format('d/m/Y') }}</span>
                </div>
                
                <div class="remesa-row">
                    <span>Remesa: {{ $producto->remesa }}</span>
                    <span>Unidad: {{ $index + 1 }} / {{ $cantidad }}</span>
                </div>
                
                <div class="info-section">
                    <p><strong>Destinatario:</strong> {{ $producto->destinatario }}</p>
                    <p><strong>Dirección:</strong> {{ $producto->direccion }}</p>
                    <p><strong>Ciudad:</strong> {{ $producto->ciudad }}</p>
                </div>
                
                <div class="cliente-section">
                    <strong>Cliente:</strong> {{ $producto->cliente }}
                </div>
                
                <div class="documento-section">
                    <strong>Documento:</strong> {{ $producto->documento ?? 'N/A' }}
                </div>
                
                <div class="qr-container">
                    <img src="data:image/png;base64,{{ $qrImage }}" alt="Código QR">
                </div>
                
                <div class="orden-section">
                    Orden de Compra
                </div>
                
                <div class="unidad-numero">
                    Unidad #{{ $index + 1 }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>