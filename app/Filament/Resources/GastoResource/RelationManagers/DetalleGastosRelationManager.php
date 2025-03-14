<?php

namespace App\Filament\Resources\GastoResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Collection;

class DetalleGastosRelationManager extends RelationManager
{
    protected static string $relationship = 'detalleGastos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nro_contrato')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('gasto_id')
            ->description('Detalle de Gasto asociado a la factura')
            ->columns([
                Tables\Columns\TextColumn::make('gasto_id')
                ->label('ID')
                ->badge()
                ->color('naranja')
                ->searchable(),
                Tables\Columns\TextColumn::make('codigo_gasto')
                ->label('Codigo')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                ->label('Empresa'),
                Tables\Columns\TextColumn::make('nro_contrato')
                ->badge()
                ->color('marronClaro'),
                Tables\Columns\TextColumn::make('valuacion_id')
                ->label('Valuacion')
                ->badge()
                ->color('marronClaro'),
                Tables\Columns\TextColumn::make('agencia.nombre'),
                
                Tables\Columns\TextColumn::make('monto_bsd')
                ->label('Monto(Bs.)')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tasa_bcv')
                ->label('Tasa BCV')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('responsable'),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha')
                ->dateTime()
                ->sortable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->label('Monto(USD)')
                    ->summarize(Sum::make()
                        ->label('Total(USD)'))
                    
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