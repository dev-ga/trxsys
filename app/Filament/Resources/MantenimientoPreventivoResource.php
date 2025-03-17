<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use App\Models\MantenimientoPreventivo;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MantenimientoPreventivoResource\Pages;
use App\Filament\Resources\MantenimientoPreventivoResource\RelationManagers;

class MantenimientoPreventivoResource extends Resource
{
    protected static ?string $model = MantenimientoPreventivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

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
                    ->searchable()
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agencia.nombre')
                    ->icon('heroicon-s-home')
                    ->searchable()
                    ->label('Agencia'),
                
                
                Tables\Columns\TextColumn::make('fecha_ejecucion')
                    ->label('Fecha Mantenimiento')
                    ->searchable()
                    ->badge()
                    ->color('marronClaro')
                    ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('fecha_prox_ejecucion')
                    ->searchable()
                    ->badge()
                    ->color('negro')
                    ->label('Proximo Mantenimiento')
                    ->dateTime('d-m-Y'),

                Tables\Columns\TextColumn::make('toneladas')
                    ->label('Toneladas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('calculo_x_tonelada')
                    ->badge()
                    ->color('success')
                    ->label('Costo por Tonelada(USD)')
                    ->money('USD')
                    ->searchable()
                    ->summarize(Sum::make()
                    ->money('USD')
                    ->label('Total(USD)')
                        ->label('Total(Bs.)'))
            ])
            ->filters([
                //
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('desde'),
                        DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['hasta'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators['desde'] = 'Venta desde ' . Carbon::parse($data['desde'])->toFormattedDateString();
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators['hasta'] = 'Venta hasta ' . Carbon::parse($data['hasta'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                SelectFilter::make('agencia')
                    ->relationship('agencia', 'nombre')
                    ->searchable()
                    ->preload()
                    ->attribute('agencia_id'),

            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            )
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