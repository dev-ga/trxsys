<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientoPreventivosRelationManager extends RelationManager
{
    protected static string $relationship = 'MantenimientoPreventivos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('agencia_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('agencia_id')
            ->columns([
            Tables\Columns\TextColumn::make('equipo_id')
                ->label('ID')
                ->badge()
                ->color('naranja')
                ->sortable(),
            Tables\Columns\TextColumn::make('agencia.nombre')
                ->searchable()
                ->icon('heroicon-s-home')
                ->label('Agencia'),
            Tables\Columns\TextColumn::make('codigo_equipo')
                ->badge()
                ->color('marronClaro')
                ->searchable(),
            Tables\Columns\TextColumn::make('toneladas')
                ->label('Toneladas')
                ->searchable(),
            Tables\Columns\TextColumn::make('calculo_x_tonelada')
                ->badge()
                ->color('success')
                ->label('Costo por Tonelada(USD)')
                ->money('USD')
                ->searchable(),
            Tables\Columns\TextColumn::make('fecha_ejecucion')
                ->label('Fecha Mantenimiento')
                ->badge()
                ->color('marronClaro')
                ->dateTime('d-m-Y'),
            Tables\Columns\TextColumn::make('fecha_prox_ejecucion')
                ->label('Proximo Mantenimiento')
                ->dateTime('d-m-Y'),
            Tables\Columns\TextColumn::make('responsable')
                ->icon('heroicon-c-user-circle')
                ->label('Cargado por:'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}