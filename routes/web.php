<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeController;
use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Página principal (welcome con escáner)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas para códigos de barras y etiquetas (usando el controlador)
Route::get('/barcode/{product}', [BarcodeController::class, 'generateBarcode'])
    ->name('barcode.image');

Route::get('/etiqueta/{product}', [BarcodeController::class, 'etiqueta'])
    ->name('etiqueta.producto');

// Ruta para registrar movimiento (entrada/salida) desde el escáner en welcome
Route::post('/registrar-movimiento', function (Request $request) {
    $request->validate([
        'barcode' => 'required|string|max:100',
        'type'    => 'required|in:entrada,salida',
    ]);

    $product = Product::where('barcode', $request->barcode)->first();

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado con ese código de barras.',
        ], 404);
    }

    // Registrar el movimiento
    \App\Models\Movement::create([
        'product_id' => $product->id,
        'barcode'    => $request->barcode,
        'type'       => $request->type,
        'quantity'   => 1,
        'notes'      => 'Registrado desde escáner en welcome',
    ]);

    // Opcional: Actualizar stock (ajusta los valores según tu lógica real)
    if ($request->type === 'entrada') {
        $product->increment('weight_current', 1000); // Ejemplo: +1 kg
    } else {
        $product->decrement('weight_current', 100);  // Ejemplo: -100 g
    }

    return response()->json([
        'success' => true,
        'message' => ($request->type === 'entrada' ? 'Entrada' : 'Salida') . ' registrada para: ' . $product->name,
        'product' => $product->only(['name', 'weight_current']),
    ]);
})->name('registrar.movimiento');