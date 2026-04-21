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
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let remesaActualId = null;
let scanner = null;
let escaneando = true;

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
        }, 150);
    } catch(e) {}
}

if (navigator.vibrate) {
    navigator.vibrate(100);
}


function iniciarEscaner() {
    scanner = new Html5QrcodeScanner("reader", { 
        fps: 10,
        qrbox: { width: 250, height: 250 }
    });

    scanner.render(onScanSuccess);
}

function onScanSuccess(decodedText) {
    if (!escaneando) return;

    escaneando = false;
    playBeep();
    scanner.pause();
    buscarProducto(decodedText);
}

function buscarProducto(codigo) {
    document.getElementById('loading').style.display = 'block';

    fetch('{{ route("escaner.buscar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ codigo })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';

        if (data.success) {
            mostrarProducto(data.producto);

            remesaActualId = data.producto.remesa_id;
            cargarEstadoRemesa(remesaActualId);

            // 🔥 reactivar automático tipo pistola
            setTimeout(() => {
                reactivarEscaner();
            }, 1500);

        } else {
            playBeep(); playBeep();
            alert(data.message);
            reactivarEscaner();
        }

        if (data.producto.pendientes === 0) {
    alert("🎉 REMESA COMPLETADA");
}
    });
}

function reactivarEscaner() {
    escaneando = true;
    scanner.resume();
}

function mostrarProducto(producto) {
    document.getElementById('resultado').style.display = 'block';

    document.getElementById('info_producto').innerHTML = `
        <p><strong>📦 Remesa:</strong> ${producto.remesa}</p>
        <p><strong>🔢 Unidad:</strong> ${producto.numero_unidad} de ${producto.total_unidades}</p>

        <p><strong>📊 Escaneadas:</strong> ${producto.escaneadas}</p>
        <p><strong>⏳ Pendientes:</strong> ${producto.pendientes}</p>

       ${producto.faltantes.length > 0 ? `
<p style="color:orange;">
    <strong>🔢 Faltan:</strong> ${producto.faltantes.join(', ')}
</p>
` : ``}

        <p><strong>👤 ${producto.destinatario}</strong></p>
    `;

    document.getElementById('botones_accion').innerHTML = `
        <div style="background:#d4edda; padding:10px; border-radius:5px;">
            ✅ Escaneado automáticamente
        </div>
    `;
}

function cargarEstadoRemesa(remesaId) {
    fetch(`/escaner/remesa/${remesaId}`)
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;

        const faltantes = data.unidades.filter(u => u.estado === 'activo');
        const entregadas = data.unidades.filter(u => u.estado === 'entregado');

        document.getElementById('panel_remesa').style.display = 'block';

        document.getElementById('estado_remesa').innerHTML = `
            <p><strong>📊 ${entregadas.length}/${data.total_unidades} entregadas</strong></p>
            <p>Faltan: ${faltantes.map(u => u.numero_unidad).join(', ')}</p>
        `;
    });
}

iniciarEscaner();
</script>

@endsection