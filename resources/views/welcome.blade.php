<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escáner de Filamentos - Registro de Entrada/Salida</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        select, button { padding: 12px; margin: 10px 0; width: 100%; font-size: 16px; border-radius: 6px; border: 1px solid #ccc; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        #reader { width: 100%; height: 300px; border: 2px solid #007bff; border-radius: 8px; overflow: hidden; margin: 20px 0; }
        #resultado { padding: 15px; margin-top: 20px; border-radius: 6px; font-weight: bold; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="container">
    <h1>Escáner de Código de Barras</h1>

    <select id="tipoMovimiento">
        <option value="entrada">Entrada (Ingreso de filamento)</option>
        <option value="salida">Salida (Uso/Egreso)</option>
    </select>

    <div id="reader"></div>

    <button id="startScan">Iniciar Cámara</button>
    <button id="stopScan" disabled>Detener Cámara</button>

    <div id="resultado"></div>
</div>

<script>
    let scanner = null;
    const readerDiv = document.getElementById('reader');
    const resultado = document.getElementById('resultado');
    const startBtn = document.getElementById('startScan');
    const stopBtn = document.getElementById('stopScan');
    const tipoSelect = document.getElementById('tipoMovimiento');

    function iniciarEscaner() {
        scanner = new Html5Qrcode("reader");

        scanner.start(
            { facingMode: "environment" }, // Cámara trasera
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                registrarMovimiento(decodedText);
                // Opcional: scanner.stop(); para detener después del primer escaneo
            },
            (err) => {
                // console.log(err);
            }
        ).catch(err => {
            resultado.innerHTML = `<div class="error">Error al iniciar cámara: ${err}</div>`;
        });

        startBtn.disabled = true;
        stopBtn.disabled = false;
    }

    function detenerEscaner() {
        if (scanner) {
            scanner.stop().then(() => {
                resultado.innerHTML += '<br>Cámara detenida.';
                startBtn.disabled = false;
                stopBtn.disabled = true;
            });
        }
    }

    async function registrarMovimiento(barcode) {
        const tipo = tipoSelect.value;

        try {
            const response = await fetch('{{ route("registrar.movimiento") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode, type: tipo })
            });

            const data = await response.json();

            if (data.success) {
                resultado.innerHTML = `<div class="success">${data.message}<br>Producto: ${data.product.name}<br>Stock actual: ${data.product.weight_current} g</div>`;
            } else {
                resultado.innerHTML = `<div class="error">${data.message}</div>`;
            }
        } catch (err) {
            resultado.innerHTML = `<div class="error">Error de conexión: ${err.message}</div>`;
        }
    }

    startBtn.addEventListener('click', iniciarEscaner);
    stopBtn.addEventListener('click', detenerEscaner);
</script>

</body>
</html>