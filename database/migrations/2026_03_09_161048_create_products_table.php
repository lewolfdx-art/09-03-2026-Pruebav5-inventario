<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Ej: "PLA Negro Inland"
            $table->string('sku')->unique();           // Código único (puede ser tu código de barras)
            $table->string('material')->nullable();    // PLA, PETG, ABS...
            $table->string('color')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('weight_initial', 8, 2)->default(1000); // gramos iniciales
            $table->decimal('weight_current', 8, 2)->default(1000);
            $table->string('barcode')->nullable();     // Aquí guardarías el código de barras como texto
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
