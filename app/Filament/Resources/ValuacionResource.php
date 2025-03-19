<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Contrato;
use Filament\Forms\Form;
use App\Models\Valuacion;
use Filament\Tables\Table;
use App\Models\Mantenimiento;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\ValuacionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ValuacionResource\RelationManagers;


class ValuacionResource extends Resource
{
    protected static ?string $model = Valuacion::class;

    protected static ?string $navigationLabel = 'Valuaciones';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $activeNavigationIcon = 'heroicon-m-currency-dollar';

    protected static ?string $recordTitleAttribute = 'descripcion';

    protected static ?string $navigationGroup = 'Gestion de Proyectos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('INFORMACION PRINCIPAL PARA LA CARGA DE LAS VALUACIONES')
                    ->description('Formulario para la carga de las valuaciones. Campos Requeridos(*)')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->schema([

                        Forms\Components\TextInput::make('codigo')
                        ->label('Codigo')
                        ->prefixIcon('heroicon-c-tag')
                        ->default(function () {
                            if (Valuacion::max('id') == null) {
                                $parte_entera = 0;
                            } else {
                                $parte_entera = Valuacion::max('id');
                            }
                            return '000' . $parte_entera + 1;
                        }),

                        Forms\Components\TextInput::make('descripcion')
                        ->label('Descripción')
                        ->placeholder('Ejemplo: valuacion 1')
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

                        Forms\Components\Select::make('contrato_id')
                            ->label('Contrato asociado')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(function (Get $get) {
                                return Contrato::where('empresa_contratante_id', $get('empresa_contratante_id'))->pluck('denominacion', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live(),

                            Forms\Components\Select::make('nro_contrato')
                            ->label('Nro. Contrato')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(function (Get $get) {
                                return Contrato::where('id', $get('contrato_id'))->pluck('nro_contrato', 'nro_contrato');
                            })
                            ->required()
                            ->searchable()
                            ->live(),

                        Forms\Components\TextInput::make('monto_usd')
                        ->label('Monto USD')
                        ->placeholder('Ejemplo: 12654.90')
                        ->hint('separador decimal (.)')
                        ->numeric()
                        ->prefixIcon('heroicon-s-pencil')
                        ->default(0.00),

                        Forms\Components\Select::make('mantenimiento_id')
                        ->label('Tipo de Mantenimiento')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options(Mantenimiento::all()->pluck('descripcion', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),

                        FileUpload::make('doc_pdf')
                        ->label('Valuacion(.pdf)')
                        ->acceptedFileTypes(['application/pdf']),


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
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('created_at')
                    ->badge()
                    ->icon('heroicon-c-calendar')
                    ->color('naranja')
                    ->label('Fecha de Registro')
                    ->dateTime('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->label('Empresa')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contrato.denominacion')
                    ->label('Contrato')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->label('Nro Contrato')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),

                Tables\Columns\TextColumn::make('monto_bsd')
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
                Tables\Columns\TextColumn::make('mantenimiento.descripcion')
                    ->badge()
                    ->color('naranja')
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('monto_usd')
                    ->badge()
                    ->color('success')
                    ->money('USD')
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Valuaciones($)')),

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
                SelectFilter::make('contrado')
                    ->relationship('contrato', 'nro_contrato')
                    ->searchable()
                    ->preload()
                    ->attribute('contrato_id'),

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

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['contrato']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Contrato' => $record->contrato->denominacion,
            'Mantenimiento' => $record->mantenimiento->descripcion,
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nro_contrato', 'codigo', 'mantenimiento_id', 'responsable', 'descripcion', 'empresaContratante.nombre', 'mantenimiento.descripcion', 'contrato.denominacion'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListValuacions::route('/'),
            'create' => Pages\CreateValuacion::route('/create'),
            'edit' => Pages\EditValuacion::route('/{record}/edit'),
        ];
    }
}