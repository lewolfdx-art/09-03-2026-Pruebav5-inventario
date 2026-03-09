<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Genera código de barras limpio y profesional:
     * - Solo barras negras
     * - Fondo blanco sólido 100%
     * - Sin número de texto debajo
     * - Márgenes limpios alrededor (quiet zones + espacio visual bonito)
     */
    public function generateBarcode(Product $product)
    {
        if (!$product->barcode) {
            abort(404, 'Este producto no tiene código de barras.');
        }

        $generator = new BarcodeGeneratorPNG();

        // Parámetros recomendados para buena legibilidad + impresión térmica
        $barWidth  = 3.0;   // grosor de barra (X-dimension ~ moderado, legible)
        $barHeight = 100;   // altura en píxeles (~1–1.5 cm en 203–300 dpi)

        $rawImage = $generator->getBarcode(
            $product->barcode,
            $generator::TYPE_CODE_128,
            $barWidth,
            $barHeight
        );

        $im = imagecreatefromstring($rawImage);
        if ($im === false) {
            abort(500, 'Error al generar el código de barras.');
        }

        $bcWidth  = imagesx($im);
        $bcHeight = imagesy($im);

        // ────────────────────────────────────────────────
        // Espacios/márgenes recomendados (en píxeles)
        // ~10–15 píxeles por lado → quiet zone + aspecto limpio
        $marginLeftRight = 15;
        $marginTopBottom = 12;

        $totalWidth  = $bcWidth  + (2 * $marginLeftRight);
        $totalHeight = $bcHeight + (2 * $marginTopBottom);

        // Nueva imagen con fondo blanco puro
        $newImage = imagecreatetruecolor($totalWidth, $totalHeight);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);

        // Copiamos el barcode centrado (con márgenes)
        $dstX = $marginLeftRight;
        $dstY = $marginTopBottom;

        imagecopy($newImage, $im, $dstX, $dstY, 0, 0, $bcWidth, $bcHeight);

        imagedestroy($im);

        // ────────────────────────────────────────────────
        // Salida PNG optimizada
        ob_start();
        imagepng($newImage, null, 9); // compresión máxima
        $finalImage = ob_get_clean();

        imagedestroy($newImage);

        return response($finalImage)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400') // 24 horas
            ->header('Content-Disposition', 'inline; filename="barcode-' . $product->id . '.png"');
    }

    public function etiqueta(Product $product)
    {
        if (!$product->barcode) {
            abort(404, 'Este producto no tiene código de barras.');
        }

        return view('etiquetas.etiqueta-filamento', compact('product'));
    }
}