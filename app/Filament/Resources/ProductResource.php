<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?int $navigationSort = 20;

    public static function getNavigationGroup(): ?string
    {
        return __('Almacén');
    }

    public static function getLabel(): ?string
    {
        return __('Producto');
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label(__('Imagen'))
                    ->image()
                    ->maxSize(4096)
                    ->placeholder(__('Imagen del producto'))
                    ->columnSpanFull(),
                
                Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Nombre'))
                            ->autofocus()
                            ->required()
                            ->minLength(2)
                            ->maxLength(100)
                            ->unique(static::getModel(), 'name', ignoreRecord:true)
                            ->columns(1),
                         TextInput::make('price')
                            ->label(__('Precio'))
                            ->required()
                            ->minLength(2)
                            ->maxLength(100)
                            ->columns(1),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->label(__('Categoria'))
                            ->searchable()
                            ->columns(1)
                    ])->columns(3),

                Textarea::make('description')
                        ->label(__('descripcion'))
                        ->required()
                        ->minLength(2)
                        ->maxLength(300)
                        ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateDescription(__('No hay productos disponibles'));
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
