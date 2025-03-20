<?php

namespace App\Filament\Resources\ContratoResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ValuacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'valuaciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contrato_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contrato_id')
            ->columns([
                
                Tables\Columns\TextColumn::make('id')
                    ->label('#Valuacion')
                    ->badge()
                    ->color('naranja')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mantenimiento.descripcion')
                    ->badge()
                    ->color('naranja')
                    ->extraAttributes(['style' => 'text-transform: capitalize;'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->label('Monto(USD)')
                    ->badge()
                    ->color('success')
                    ->money('USD')
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()
                        ->money('USD')
                        ->label('Total(USD)')
                    ),
                    
                //CAMPOS OCULTOS
                //------------------------------------------------
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->label('Monto(Bs.)')
                    ->badge()
                    ->color('success')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                //---------------------------------------------------
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
            ]);
    }
}