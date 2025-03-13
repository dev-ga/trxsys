<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MantenimientoCorrectivoResource\Pages;
use App\Filament\Resources\MantenimientoCorrectivoResource\RelationManagers;
use App\Models\MantenimientoCorrectivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientoCorrectivoResource extends Resource
{
    protected static ?string $model = MantenimientoCorrectivo::class;

    protected static ?string $navigationIcon = 'heroicon-m-shield-check';


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
                        ->color('marronClaro'),
                Tables\Columns\TextColumn::make('agencia.nombre'),
                Tables\Columns\TextColumn::make('nro_presupuesto')
                    ->label('Nro. Presupuesto')
                    ->badge()
                    ->color('marronClaro')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_presupuesto_usd')
                    ->badge()
                    ->money('USD')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('detalles'),
                Tables\Columns\TextColumn::make('doc_pdf'),
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
            'index' => Pages\ListMantenimientoCorrectivos::route('/'),
            'create' => Pages\CreateMantenimientoCorrectivo::route('/create'),
            'edit' => Pages\EditMantenimientoCorrectivo::route('/{record}/edit'),
        ];
    }
}