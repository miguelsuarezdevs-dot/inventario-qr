@extends('layouts.app')

@section('title', 'Escanear QR')

@section('content')
<div class="card">
    <div class="card-header">📷 Escanear Código QR</div>
    
    <div class="grid" style="grid-template-columns: 1fr 1fr;">
        <!-- Lado izquierdo: Escáner -->
        <div>
            <div id="reader" style="width: 100%;"></div>
            <p class="text-gray text-center" style="margin-top: 10px;">
                Acerca el código QR a la cámara
            </p>
            
            <div style="margin-top: 15px;">
                <label>O ingresar código manual:</label>
                <div style="display: flex; gap: 10px; margin-top: 5px;">
                    <input type="text" id="codigo_manual" placeholder="Pega el código QR aquí" style="flex: 1;">
                    <button onclick="buscarManual()" class="btn btn-info">Buscar</button>
                </div>
            </div>
        </div>
        
        <!-- Lado derecho: Resultado del último escaneo -->
        <div>
            <div id="resultado" style="display: none;">
                <div style="background: #e8f4f8; border-radius: 10px; padding: 15px;">
                    <h3 style="margin-bottom: 15px;">📦 Producto escaneado</h3>
                    <div id="info_producto" style="margin-bottom: 15px;"></div>
                    <div id="botones_accion"></div>
                </div>
            </div>
            
            <div id="loading" style="display: none; text-align: center; padding: 40px;">
                <div>🔍 Buscando producto...</div>
            </div>
        </div>
    </div>
    
    <!-- Panel de estado de la remesa actual -->
    <div id="panel_remesa" style="display: none; margin-top: 20px;">
        <div class="card">
            <div class="card-header">📊 Estado de la Remesa</div>
            <div id="estado_remesa"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let remesaActualId = null;
    let scanner = null;
    let escaneando = true;
    
    // Sonido beep
    function playBeep() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 880;
            gainNode.gain.value = 0.3;
            
            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
                audioContext.close();
            }, 200);
        } catch(e) {
            console.log('Sonido no disponible');
        }
    }
    
    // Inicializar escáner
    function iniciarEscaner() {
        if (scanner) {
            scanner.clear();
        }
        
        scanner = new Html5QrcodeScanner("reader", { 
            fps: 10,
            qrbox: { width: 250, height: 250 },
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true
        });
        
        scanner.render(onScanSuccess, onScanError);
    }
    
    function onScanSuccess(decodedText) {
        if (!escaneando) return;
        
        playBeep();
        escaneando = false;
        scanner.pause();
        buscarProducto(decodedText);
    }
    
    function onScanError(error) {
        // No hacer nada
    }
    
    function buscarProducto(codigo) {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('resultado').style.display = 'none';
        
        fetch('{{ route("escaner.buscar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';
            
            if (data.success) {
                mostrarProducto(data.producto);
                
                // Si cambió la remesa, cargar su estado
                if (data.producto.remesa_id !== remesaActualId) {
                    remesaActualId = data.producto.remesa_id;
                    cargarEstadoRemesa(remesaActualId);
                } else {
                    // Si es la misma remesa, recargar estado
                    cargarEstadoRemesa(remesaActualId);
                }
            } else {
                alert('❌ ' + data.message);
                reactivarEscaner();
            }
        })
        .catch(error => {
            document.getElementById('loading').style.display = 'none';
            alert('Error al buscar el producto');
            reactivarEscaner();
        });
    }
    
    function reactivarEscaner() {
        escaneando = true;
        scanner.resume();
        document.getElementById('resultado').style.display = 'none';
    }
    
    function mostrarProducto(producto) {
        document.getElementById('resultado').style.display = 'block';
        
        let estadoTexto = '';
        let estadoColor = '';
        
        switch(producto.estado) {
            case 'activo':
                estadoTexto = '✅ PENDIENTE';
                estadoColor = '#28a745';
                break;
            case 'entregado':
                estadoTexto = '📦 ENTREGADO';
                estadoColor = '#6c757d';
                break;
            case 'devuelto':
                estadoTexto = '↩️ DEVUELTO';
                estadoColor = '#dc3545';
                break;
        }
        
        document.getElementById('info_producto').innerHTML = `
            <p><strong>📦 Remesa:</strong> ${producto.remesa}</p>
            <p><strong>🏢 Sucursal:</strong> ${producto.sucursal}</p>
            <p><strong>🔢 Unidad:</strong> ${producto.numero_unidad} de ${producto.total_unidades}</p>
            <p><strong>👤 Destinatario:</strong> ${producto.destinatario}</p>
            <p><strong>📍 Ciudad:</strong> ${producto.ciudad}</p>
            <p><strong>🏠 Cliente:</strong> ${producto.cliente}</p>
            <p><strong>📌 Estado:</strong> <span style="color: ${estadoColor}; font-weight: bold;">${estadoTexto}</span></p>
        `;
        
        let botones = '';
        if (producto.estado === 'activo') {
            botones = `
                <button onclick="procesarAccion('entregado')" class="btn btn-primary" style="width: 100%;">
                    ✅ Marcar como Entregado
                </button>
            `;
        } else if (producto.estado === 'entregado') {
            botones = `
                <button onclick="procesarAccion('devuelto')" class="btn btn-danger" style="width: 100%;">
                    ↩️ Marcar como Devuelto
                </button>
            `;
        } else {
            botones = `
                <div style="background: #f8d7da; padding: 10px; border-radius: 5px; text-align: center;">
                    ⚠️ Esta unidad ya fue procesada
                </div>
                <button onclick="reactivarEscaner()" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                    🔄 Escanear siguiente
                </button>
            `;
        }
        document.getElementById('botones_accion').innerHTML = botones;
    }
    
    function cargarEstadoRemesa(remesaId) {
        fetch(`/escaner/remesa/${remesaId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarEstadoRemesa(data);
                document.getElementById('panel_remesa').style.display = 'block';
            }
        });
    }
    
    function mostrarEstadoRemesa(data) {
        const faltantes = data.unidades.filter(u => u.estado === 'activo');
        const entregadas = data.unidades.filter(u => u.estado === 'entregado');
        const devueltas = data.unidades.filter(u => u.estado === 'devuelto');
        
        let faltantesHtml = '';
        if (faltantes.length > 0) {
            faltantesHtml = `
                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 15px; margin-top: 10px;">
                    <strong>⚠️ UNIDADES FALTANTES POR ESCANEAR:</strong>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        ${faltantes.map(u => `
                            <div style="background: #ffc107; padding: 8px 12px; border-radius: 5px; font-weight: bold;">
                                🎫 Unidad ${u.numero_unidad}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else {
            faltantesHtml = `
                <div style="background: #d4edda; border: 1px solid #28a745; border-radius: 5px; padding: 15px; margin-top: 10px;">
                    ✅ ¡TODAS LAS UNIDADES HAN SIDO ENTREGADAS!
                </div>
            `;
        }
        
        const porcentaje = (entregadas.length / data.total_unidades) * 100;
        
        document.getElementById('estado_remesa').innerHTML = `
            <div style="margin-bottom: 15px;">
                <p><strong>📦 Remesa:</strong> ${data.remesa}</p>
                <p><strong>🏢 Sucursal:</strong> ${data.sucursal}</p>
                <p><strong>👤 Destinatario:</strong> ${data.destinatario}</p>
                <p><strong>📍 Ciudad:</strong> ${data.ciudad}</p>
            </div>
            
            <div style="margin: 15px 0;">
                <strong>📊 PROGRESO:</strong>
                <div style="background: #e9ecef; border-radius: 10px; height: 25px; margin-top: 5px;">
                    <div style="background: #28a745; width: ${porcentaje}%; height: 25px; border-radius: 10px; text-align: center; color: white; font-size: 12px; line-height: 25px;">
                        ${entregadas.length}/${data.total_unidades} entregadas
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 20px; margin: 15px 0;">
                <div style="background: #d4edda; padding: 10px; border-radius: 5px; flex: 1; text-align: center;">
                    <strong>✅ Entregadas</strong><br>
                    <span style="font-size: 24px;">${entregadas.length}</span>
                </div>
                <div style="background: #fff3cd; padding: 10px; border-radius: 5px; flex: 1; text-align: center;">
                    <strong>⏳ Pendientes</strong><br>
                    <span style="font-size: 24px;">${faltantes.length}</span>
                </div>
                <div style="background: #f8d7da; padding: 10px; border-radius: 5px; flex: 1; text-align: center;">
                    <strong>↩️ Devueltas</strong><br>
                    <span style="font-size: 24px;">${devueltas.length}</span>
                </div>
            </div>
            
            ${faltantesHtml}
        `;
    }
    
    function procesarAccion(accion) {
        fetch('{{ route("escaner.procesar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                producto_id: document.getElementById('info_producto').getAttribute('data-producto-id') || 
                    (() => { 
                        const id = prompt('Ingrese el ID del producto:');
                        return id;
                    })()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                playBeep();
                alert(data.message);
                // Recargar estado
                cargarEstadoRemesa(remesaActualId);
                reactivarEscaner();
            } else {
                alert('❌ ' + data.message);
                reactivarEscaner();
            }
        });
    }
    
    // Guardar producto ID al mostrar
    function mostrarProductoConId(producto) {
        document.getElementById('info_producto').setAttribute('data-producto-id', producto.id);
        mostrarProducto(producto);
    }
    
    function buscarManual() {
        const codigo = document.getElementById('codigo_manual').value.trim();
        if (codigo) {
            buscarProducto(codigo);
        } else {
            alert('Ingresa un código QR');
        }
    }
    
    // Iniciar
    iniciarEscaner();
</script>
@endsection