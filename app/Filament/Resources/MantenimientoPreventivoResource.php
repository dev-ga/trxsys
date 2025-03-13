<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MantenimientoPreventivoResource\Pages;
use App\Filament\Resources\MantenimientoPreventivoResource\RelationManagers;
use App\Models\MantenimientoPreventivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientoPreventivoResource extends Resource
{
    protected static ?string $model = MantenimientoPreventivo::class;

    protected static ?string $navigationIcon = 'heroicon-m-shield-exclamation';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipo_id')
                    ->label('ID')
                    ->badge()
                    ->color('naranja')
                    ->sortable(),
                Tables\Columns\TextColumn::make('codigo_equipo')
                        ->badge()
                        ->color('marronClaro')
                        ->searchable(),
                Tables\Columns\TextColumn::make('agencia.nombre')
                    ->searchable()
                    ->label('Agencia'),
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
                    ->badge()
                    ->color('negro')
                    ->label('Proximo Mantenimiento')
                    ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('responsable'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMantenimientoPreventivos::route('/'),
            'create' => Pages\CreateMantenimientoPreventivo::route('/create'),
            'edit' => Pages\EditMantenimientoPreventivo::route('/{record}/edit'),
        ];
    }
}