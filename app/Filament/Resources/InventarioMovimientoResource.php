<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioMovimientoResource\Pages;
use App\Filament\Resources\InventarioMovimientoResource\RelationManagers;
use App\Models\InventarioMovimiento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class InventarioMovimientoResource extends Resource
{
    protected static ?string $model = InventarioMovimiento::class;

    protected static ?string $navigationIcon = 'heroicon-m-arrows-right-left';

    protected static ?string $recordTitleAttribute = 'articulo.descripcion';

    protected static ?string $navigationGroup = 'Manejo de Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('inventario_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('articulo_id')
                    ->relationship('articulo', 'id')
                    ->required(),
                Forms\Components\TextInput::make('almacen_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tipo_movimiento')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('responsable')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventario_id')
                    ->label('Inventario ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('articulo.descripcion')
                    ->label('Articulo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('almacen.descripcion')
                    ->label('Almacen')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_movimiento')
                    ->label('Movimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nro_factura')
                    ->label('Nro. Factura')
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Realizado el:')
                    ->dateTime('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['articulo.descripcion', 'almacen_id', 'codigo_articulo', 'tipo_movimiento', 'nro_factura', 'responsable', 'inventario_id', 'articulo_id', 'almacen.descripcion'];
    }
    

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['articulo']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Artículo' => $record->articulo->descripcion,
        ];
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
            'index' => Pages\ListInventarioMovimientos::route('/'),
            'create' => Pages\CreateInventarioMovimiento::route('/create'),
            'edit' => Pages\EditInventarioMovimiento::route('/{record}/edit'),
        ];
    }
}