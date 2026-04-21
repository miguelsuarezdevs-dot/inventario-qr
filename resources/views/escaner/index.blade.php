@extends('layouts.app')

@section('title', 'Escanear QR')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-4">📷 Escanear Código QR</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Lado izquierdo: Escáner --}}
        <div>
            <div class="bg-gray-100 rounded-lg p-4">
                <div id="reader" style="width: 100%;"></div>
                <p class="text-sm text-gray-500 text-center mt-2">
                    Acerca el código QR a la cámara
                </p>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">O ingresar código manual:</label>
                <div class="flex gap-2">
                    <input type="text" id="codigo_manual" placeholder="Escribe o pega el código QR" 
                           class="flex-1 border rounded-lg p-2">
                    <button onclick="buscarManual()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 rounded-lg">
                        Buscar
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Lado derecho: Resultado --}}
        <div>
            <div id="resultado" class="hidden">
                <div class="border rounded-lg p-4 bg-blue-50">
                    <h3 class="font-bold text-lg mb-3">📦 Producto encontrado</h3>
                    
                    <div id="info_producto" class="space-y-2 mb-4"></div>
                    
                    <div class="border-t pt-4 mt-4">
                        <label class="block text-sm font-medium mb-2">Cantidad:</label>
                        <div class="flex gap-2">
                            <input type="number" id="cantidad" value="1" min="1" 
                                   class="w-32 border rounded-lg p-2 text-center">
                            <button onclick="actualizarStock('entrada')" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                ➕ Agregar
                            </button>
                            <button onclick="actualizarStock('salida')" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                ➖ Retirar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="loading" class="hidden text-center py-8">
                <div class="text-gray-500">Buscando producto...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let productoActual = null;
    
    // Inicializar escáner
    const html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
        fps: 10,
        qrbox: { width: 250, height: 250 },
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true,
        // Desactiva completamente el modo "subir imagen" y deja solo cámara.
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
    });
    
    function onScanSuccess(decodedText, decodedResult) {
        // Detener escáner temporalmente
        html5QrcodeScanner.pause();
        
        // Buscar producto
        buscarProducto(decodedText);
    }

    function onScanError(errorMessage) {
        // Ignoramos errores de lectura intermitentes para no saturar la UI.
    }

    // Iniciar cámara + lector QR al cargar la vista.
    html5QrcodeScanner.render(onScanSuccess, onScanError);
    
    function buscarProducto(codigo) {
        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('resultado').classList.add('hidden');
        
        fetch('/escaner/buscar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').classList.add('hidden');
            
            if (data.success) {
                productoActual = data.producto;
                mostrarProducto(productoActual);
            } else {
                alert('❌ Producto no encontrado en el sistema');
                // Reactivar escáner
                html5QrcodeScanner.resume();
            }
        })
        .catch(error => {
            document.getElementById('loading').classList.add('hidden');
            alert('Error al buscar el producto');
            html5QrcodeScanner.resume();
        });
    }
    
    function mostrarProducto(producto) {
        document.getElementById('resultado').classList.remove('hidden');
        
        let estadoColor = '';
        switch(producto.estado) {
            case 'activo': estadoColor = 'text-green-600'; break;
            case 'entregado': estadoColor = 'text-gray-600'; break;
            case 'parcial': estadoColor = 'text-yellow-600'; break;
            default: estadoColor = 'text-blue-600';
        }
        
        document.getElementById('info_producto').innerHTML = `
            <p><strong>📌 Remesa:</strong> ${producto.remesa}</p>
            <p><strong>🏢 Cliente:</strong> ${producto.cliente}</p>
            <p><strong>👤 Destinatario:</strong> ${producto.destinatario}</p>
            <p><strong>📍 Ciudad:</strong> ${producto.ciudad}</p>
            <p><strong>🏠 Ubicación:</strong> ${producto.ubicacion}</p>
            <p><strong>📊 Stock actual:</strong> <span class="text-xl font-bold">${producto.unidades_actuales}</span> unidades</p>
            <p><strong>📌 Estado:</strong> <span class="${estadoColor} font-bold">${producto.estado.toUpperCase()}</span></p>
        `;
        
        // Resetear cantidad
        document.getElementById('cantidad').value = 1;
    }
    
    function buscarManual() {
        const codigo = document.getElementById('codigo_manual').value.trim();
        if (codigo) {
            buscarProducto(codigo);
        } else {
            alert('Ingresa un código QR');
        }
    }
    
    function actualizarStock(accion) {
        if (!productoActual) {
            alert('Primero escanea un producto');
            return;
        }
        
        const cantidad = parseInt(document.getElementById('cantidad').value);
        
        if (!cantidad || cantidad < 1) {
            alert('Ingresa una cantidad válida');
            return;
        }
        
        // Deshabilitar botones temporalmente
        const btns = document.querySelectorAll('#resultado button');
        btns.forEach(btn => btn.disabled = true);
        
        fetch('/escaner/procesar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                producto_id: productoActual.id,
                accion: accion,
                cantidad: cantidad
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Actualizar stock mostrado
                productoActual.unidades_actuales = data.stock_actual;
                productoActual.estado = data.estado;
                mostrarProducto(productoActual);
                
                // Reactivar escáner para nuevo escaneo
                html5QrcodeScanner.resume();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('Error al procesar la solicitud');
        })
        .finally(() => {
            btns.forEach(btn => btn.disabled = false);
        });
    }
</script>
@endsection