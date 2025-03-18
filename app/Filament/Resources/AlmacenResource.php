<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Almacen;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AlmacenResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AlmacenResource\RelationManagers;

class AlmacenResource extends Resource
{
    protected static ?string $model = Almacen::class;

    protected static ?string $navigationLabel = 'Almacenes';

    protected static ?string $recordTitleAttribute = 'descripcion';

    protected static ?string $navigationIcon = 'heroicon-o-building-office'; //building

    protected static ?string $activeNavigationIcon = 'heroicon-s-building-office';

    protected static ?string $navigationGroup = 'Manejo de Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Almacen')
                ->description('Formulario para la carga de almacenes. Campos Requeridos(*)')
                ->icon('heroicon-o-building-office')
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->label('Codigo')
                        ->prefixIcon('heroicon-c-tag')
                        ->default(function () {
                            if (Almacen::max('id') == null) {
                                $parte_entera = 0;
                            } else {
                                $parte_entera = Almacen::max('id');
                            }
                            return '000' . $parte_entera + 1;
                        })
                    ->disabled()
                    ->dehydrated(),
                    Forms\Components\TextInput::make('descripcion')
                        ->label('Descripción del gasto')
                        ->prefixIcon('heroicon-s-pencil')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('responsable')
                        ->prefixIcon('heroicon-c-user-circle')
                        ->label('Cargado por:')
                        ->disabled()
                        ->dehydrated()
                        ->default(Auth::user()->name),

                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->extraAttributes(['style' => 'text-transform: capitalize;'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAlmacens::route('/'),
            'create' => Pages\CreateAlmacen::route('/create'),
            'edit' => Pages\EditAlmacen::route('/{record}/edit'),
        ];
    }
}