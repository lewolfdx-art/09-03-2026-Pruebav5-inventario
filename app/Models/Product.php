<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',          // ← Agrega este (o el nombre real del campo)
        'sku',
        'material',
        'color',
        'brand',
        'weight_initial',
        'weight_current',
        'barcode',
        'notes',
        // Agrega aquí TODOS los campos que usas en el formulario de Filament
    ];
}