<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta - {{ $product->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        .etiqueta {
            width: 80mm;          /* Ancho: ajusta entre 60-85mm según tu impresora */
            height: 30mm;         /* Alto: mitad aproximada de DNI (54mm / 2 ≈ 27mm) */
            background: white;
            border: 1px dashed #ccc; /* Solo para ver bordes al imprimir, quítalo si no quieres */
            box-sizing: border-box;
            padding: 4mm 6mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .titulo {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .barcode-img {
    width: 100%;
    height: auto;
    max-height: 20mm; /* Ajusta a 18–25mm para que quepa en etiqueta */
    image-rendering: crisp-edges; /* Para que no se vea borroso al imprimir */
}
        .info {
            font-size: 8pt;
            text-align: center;
        }
        @media print {
            body { margin: 0; }
            .etiqueta { border: none; page-break-after: always; }
        }
    </style>
</head>
<body onload="window.print();">  <!-- Imprime automáticamente al abrir -->

<div class="etiqueta">
    <div class="titulo">{{ $product->name }}</div>

    <img class="barcode-img"
         src="{{ route('barcode.image', $product) }}"
         alt="Código de barras {{ $product->barcode }}">

    <div class="info">
        {{ $product->sku ?? '' }} • {{ $product->material ?? '' }} • {{ $product->color ?? '' }}
        <br>
        Peso actual: {{ number_format($product->weight_current, 0) }} g
    </div>
</div>

</body>
</html>