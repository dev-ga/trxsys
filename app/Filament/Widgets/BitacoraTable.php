<?php

namespace App\Filament\Widgets;

use App\Models\Bitacora;
use App\Models\Order;
use Carbon\Carbon;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class BitacoraTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    // protected static ?int $sort = 3;

    protected static ?string $heading = 'Bitacora';

    protected function getTableQuery(): Builder
    {
        return Bitacora::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [

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
                ->label('Trabajo Realizado'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha')
                ->dateTime('d-m-Y'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
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
        ];
    }

}
