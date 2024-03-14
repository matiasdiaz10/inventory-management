<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'OrderLines';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Lineas del pedido # :id', ['id' => $ownerRecord->id]);
    }

    public static function getRecordLabel(): ?string
    {
        return __('Linea de pedido');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('order_id') //Campo oculto
                    ->default($this->ownerRecord->id),

                Grid::make()
                    ->columns(3)
                    ->schema([
                        Select::make('product_id')
                            ->label(__('Producto'))
                            ->placeholder(__('Selecciona un producto'))
                            ->options(
                                Product::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            // SELECCION DEL PRODUCTO Y SE CARGAN EL PRECIO DEL MISMO
                            // Para que sea reactivo el form
                            ->reactive()
                            // Cuando seleccionamos el producto, busca el producto en DB
                            // Luego se establece el unit_price del precio del producto para la columna
                            ->afterStateUpdated(function (Select $component, Set $set) {
                                $product = Product::query()
                                    ->where('id', $component->getState())
                                    ->first();
                                
                                $set('unit_price', $product?->price ?? 0);
                            }),
                        TextInput::make('quantity')
                            ->numeric()
                            ->label(__('Cantidad'))
                            ->required()
                            ->placeholder(__('Cantidad del producto'))
                            ->default(1),
                        TextInput::make('unit_price')
                            ->label(__('Precio Unitario'))
                            ->required()
                            ->placeholder(__('Precio unitario del producto'))
                            ->default(0)
                            //->disabled()
                            ->suffix('$ '),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('Producto'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $product) => $product->description),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Cantidad'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label(__('Precio unitario'))
                    ->sortable()
                    ->money('ARG'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('Precio total'))
                    ->sortable()
                    ->money('ARG')
                    ->state(function (Model $item):float {
                        return $item->quantity * $item->unit_price;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateDescription(__('No tiene Pedidos disponibles'));
    }
}
