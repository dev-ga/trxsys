<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Estado;
use App\Models\Agencia;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AgenciaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AgenciaResource\RelationManagers;

class AgenciaResource extends Resource
{
    protected static ?string $model = Agencia::class;

    protected static ?string $navigationIcon = 'heroicon-s-home'; //heroicon-s-home

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            Forms\Components\Section::make('AGENCIAS')
                ->description('Formulario para la carga de agencias.')
                ->icon('heroicon-s-home')
                ->schema([

                    Forms\Components\TextInput::make('codigo')
                        ->label('Codigo')
                        ->prefixIcon('heroicon-c-tag')
                        ->required(),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre')
                        ->prefixIcon('heroicon-s-pencil')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('empresa_contratante_id')
                        ->label('Empresa Contratante')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options(EmpresaContratante::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('estado_id')
                        ->label('Estado')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options(Estado::all()->pluck('descripcion', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('direccion')
                        ->label('Direccion')
                        ->prefixIcon('heroicon-s-pencil')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('responsable')
                        ->prefixIcon('heroicon-c-user-circle')
                        ->label('Cargado por:')
                        ->disabled()
                        ->dehydrated()
                        ->default(Auth::user()->name),


                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Codigo')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre/Razon Social')
                    ->icon('heroicon-s-home')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
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

    //Relacion 1 a N con la tabla de Bitacoras
    public static function getRelations(): array
    {
        return [
            RelationManagers\EquiposRelationManager::class,
            RelationManagers\MantenimientoPreventivosRelationManager::class,
            RelationManagers\MantenimientoCorrectivosRelationManager::class,
            RelationManagers\BitacorasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencias::route('/'),
            'create' => Pages\CreateAgencia::route('/create'),
            'edit' => Pages\EditAgencia::route('/{record}/edit'),
        ];
    }
}