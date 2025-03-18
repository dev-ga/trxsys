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
use App\Models\MantenimientoCorrectivo;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MantenimientoCorrectivoResource\Pages;
use App\Filament\Resources\MantenimientoCorrectivoResource\RelationManagers;

class MantenimientoCorrectivoResource extends Resource
{
    protected static ?string $model = MantenimientoCorrectivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $activeNavigationIcon = 'heroicon-s-shield-check';

    protected static ?string $recordTitleAttribute = 'codigo_equipo';

    protected static ?string $navigationGroup = 'Gestion de Proyectos';

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
                    ->label('Código de equipo')
                    ->searchable()
                    ->badge()
                    ->color('marronClaro'),
                Tables\Columns\TextColumn::make('agencia.nombre')
                    ->searchable()
                    ->icon('heroicon-s-home'),

                Tables\Columns\TextColumn::make('detalles'),
                Tables\Columns\TextColumn::make('nro_presupuesto')
                    ->searchable()
                    ->label('Nro. Presupuesto')
                    ->badge()
                    ->color('marronClaro')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_presupuesto_usd')
                    ->numeric()
                    ->money('USD')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()
                        ->money('USD')
                        ->label('Total(USD)')
                        ->label('Total(Bs.)'))
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('naranja'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                    Tables\Actions\Action::make('ver_pdf')
                        ->label('Ver PDF')
                        ->icon('heroicon-s-eye')
                        ->color('naranja')
                        ->url(function ($record) {
                            return asset('storage/' . $record->doc_pdf);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nro_presupuesto', 'detalles', 'agencia.nombre', 'responsable', 'codigo_equipo'];
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