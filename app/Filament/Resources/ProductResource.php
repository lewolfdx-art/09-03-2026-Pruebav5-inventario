<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $label = 'Producto';
    protected static ?string $pluralLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del producto')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: PLA Negro Inland 1.75mm')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('sku')
                    ->label('SKU / Código interno')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('Ej: PLA-NEG-001'),

                Forms\Components\TextInput::make('material')
                    ->label('Material')
                    ->maxLength(100)
                    ->placeholder('Ej: PLA, PETG, ABS'),

                Forms\Components\TextInput::make('color')
                    ->label('Color')
                    ->maxLength(100)
                    ->placeholder('Ej: Negro, Rojo, Transparente'),

                Forms\Components\TextInput::make('brand')
                    ->label('Marca')
                    ->maxLength(100)
                    ->placeholder('Ej: Inland, eSun, Creality'),

                Forms\Components\TextInput::make('weight_initial')
                    ->label('Peso inicial (gramos)')
                    ->required()
                    ->numeric()
                    ->default(1000)
                    ->minValue(0)
                    ->step(1),

                Forms\Components\TextInput::make('weight_current')
                    ->label('Peso actual (gramos)')
                    ->required()
                    ->numeric()
                    ->default(1000)
                    ->minValue(0)
                    ->step(1),

                Forms\Components\TextInput::make('barcode')
                    ->label('Código de barras')
                    ->maxLength(100)
                    ->placeholder('Ej: 7894561230000 o PLA001')
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('notes')
                    ->label('Notas / Observaciones')
                    ->columnSpanFull()
                    ->rows(4)
                    ->placeholder('Ej: Filamento seco, lote #45, caducidad 2027'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('material')
                    ->label('Material')
                    ->searchable(),

                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->searchable(),

                Tables\Columns\TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),

                Tables\Columns\TextColumn::make('weight_initial')
                    ->label('Peso inicial')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('weight_current')
                    ->label('Peso actual')
                    ->numeric()
                    ->sortable()
                    ->description(fn ($record) => $record->weight_current < 200 ? '¡Bajo stock!' : null),

                // Columna del barcode: usa la misma ruta que la etiqueta/imagen
                Tables\Columns\ImageColumn::make('barcode_image')
                    ->label('Código de Barras')
                    ->getStateUsing(fn (Product $record) => $record->barcode ? route('barcode.image', $record) : null)
                    ->size(240)  // ← Tamaño más grande para ver bien el detalle (igual que impresión)
                    ->extraImgAttributes([
                        'alt' => 'Código de barras del producto',
                        'loading' => 'lazy',
                        'style' => 'image-rendering: crisp-edges; width: 100%; height: auto;', // Nitidez y proporción real
                    ])
                    ->placeholder('Sin código de barras'),

                Tables\Columns\TextColumn::make('barcode')
                    ->label('Código (texto)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('etiqueta')
                    ->label('Imprimir Etiqueta')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Product $record) => route('etiqueta.producto', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}