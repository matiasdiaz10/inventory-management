<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->directory('form-attachments')
                    ->visibility('private')
                    ->preserveFilenames()
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
                ImageColumn::make('image')
                    ->label(__('Imagen')),
                TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $product) => $product->description),
                TextColumn::make('price')
                    ->label(__('Precio'))
                    ->money('ARG')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('Categoria'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Creado'))
                    ->sortable()
                    ->date('d/m/Y H:i'),
                TextColumn::make('updated_at')
                    ->label(__('Actualizado'))
                    ->sortable()
                    ->date('d/m/Y H:i')
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category','name')
                    ->label(__('Categoria'))
                    /* ->searchable(), */
            ])
            ->actions([
                
                Tables\Actions\ViewAction::make(),
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
            ->emptyStateDescription(__('No hay productos disponibles'));
    }
    
    //Metodo que Visualiza la informacion del producto (se abre un modal)

    public static function infolist(Infolist $infolist) : Infolist
    {
        return $infolist
            ->schema([
                ImageEntry::make('image')
                    ->hiddenLabel()
                    ->columnSpanFull(),
                Section::make()->schema([
                    TextEntry::make('name')
                        ->label(__('Nombre')),
                    TextEntry::make('price')
                        ->label(__('Precio'))->money('ARG'),
                    TextEntry::make('category.name')
                        ->label(__('Categoria')),
                ])->columns(3)
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
