<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Agencia;
use App\Models\Bitacora;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BitacoraResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BitacoraResource\RelationManagers;

class BitacoraResource extends Resource
{
    protected static ?string $model = Bitacora::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agencia.nombre')
                    ->searchable()
                    ->icon('heroicon-s-home')
                    ->label('Agencia'),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->searchable()
                    ->badge()
                    ->color('marronClaro')
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->searchable()
                    ->badge()
                    ->color('naranja')
                    ->label('Nro Contrato'),
                Tables\Columns\TextColumn::make('image')
                    ->label('Imagen')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('doc_pdf')
                    ->label('PDF')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('valuacion.descripcion')
                    ->searchable()
                    ->badge()
                    ->color('azul')
                    ->label('Valuacion'),
                Tables\Columns\TextColumn::make('mantenimiento.descripcion')
                    ->searchable()
                    ->label('Tipo de Mantenimiento')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'preventivo' => 'naranja',
                            'correctivo' => 'success',
                            default      => 'naranja',
                        };
                    })
                    ->icon(function ($state) {
                        return match ($state) {
                            'preventivo' => 'heroicon-m-shield-exclamation',
                            'correctivo' => 'heroicon-m-shield-check',
                            default      => 'heroicon-s-wrench',
                        };
                    }),
                Tables\Columns\TextColumn::make('responsable')
                    ->label('Responsable')
                    ->icon('heroicon-c-user-circle')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('trabajo_realizado')
                    ->searchable()
                    ->label('Trabajo Realizado')
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d-m-Y'),
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
            'index' => Pages\ListBitacoras::route('/'),
            'create' => Pages\CreateBitacora::route('/create'),
            'edit' => Pages\EditBitacora::route('/{record}/edit'),
        ];
    }
}