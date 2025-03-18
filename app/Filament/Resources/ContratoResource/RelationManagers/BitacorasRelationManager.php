<?php

namespace App\Filament\Resources\ContratoResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Agencia;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Contrato;
use Filament\Forms\Form;
use App\Models\Valuacion;
use Filament\Tables\Table;
use App\Models\Mantenimiento;
use App\Models\EmpresaContratante;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Models\MantenimientoCorrectivo;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class BitacorasRelationManager extends RelationManager
{
    protected static string $relationship = 'bitacoras';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('BITACORA DE EJECUCION')
                    ->description('Formulario para la carga de bitacoras por agencia. ')
                    ->icon('heroicon-m-arrow-trending-down')
                    ->schema([
                        Grid::make(2)->schema([

                        Forms\Components\Select::make('agencia_id')
                            ->label('Agencia')
                            ->options(function (RelationManager $livewire) {
                                return Agencia::where('contrato_id', $livewire->ownerRecord->id)->get()->pluck('nombre', 'id');
                            })
                            ->preload()
                            ->searchable(),

                            Forms\Components\Select::make('empresa_contratante_id')
                            ->relationship('empresaContratante', 'nombre')
                            ->label('Empresa Contratante')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->default(function (RelationManager $livewire) {
                                $empresa_id = $livewire->ownerRecord->empresa_contratante_id;
                                return $empresa_id;
                            })
                            ->disabled()
                            ->dehydrated(),

                            Forms\Components\Select::make('contrato_id')
                                ->label('Contrato asociado')
                                ->relationship('contrato', 'denominacion')
                                ->searchable()
                                ->required()
                                ->default(function (RelationManager $livewire) {
                                    return $livewire->ownerRecord->id;
                                })
                                ->live()
                                ->disabled()
                                ->dehydrated(),

                            Forms\Components\Select::make('nro_contrato')
                                ->label('Nro. Contrato')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->default(function (RelationManager $livewire) {
                                    return Contrato::where('id', $livewire->ownerRecord->id)->first()->nro_contrato;
                                })
                                ->required()
                                ->searchable()
                                ->live(),

                            Forms\Components\Select::make('valuacion_id')
                                ->label('Valuacion')
                                ->options(function (RelationManager $livewire) {
                                    return Valuacion::where('contrato_id', $livewire->ownerRecord->id)
                                        ->get()->pluck('descripcion', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->preload(),

                            Forms\Components\Select::make('mantenimiento_id')
                                ->label('Tipo de Mantenimiento')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options(Mantenimiento::all()->pluck('descripcion', 'id'))
                                ->searchable()
                                ->required()
                                ->live(),

                            Forms\Components\TextInput::make('responsable')
                                ->prefixIcon('heroicon-s-home')
                                ->label('Cargado por:')
                                ->disabled()
                                ->dehydrated()
                                ->default(Auth::user()->name),

                        ]),

                        Grid::make(3)->schema([

                            Forms\Components\Select::make('equipo_id')
                                ->label('Tipo de Mantenimiento')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options(function (Get $get, RelationManager $livewire) {

                                    $mantenimiento = Mantenimiento::where('id', $get('mantenimiento_id'))->first();

                                    if (isset($mantenimiento) && $mantenimiento->descripcion == 'correctivo') {
                                        return MantenimientoCorrectivo::where('agencia_id', $livewire->ownerRecord->id)->pluck('codigo_equipo', 'codigo_equipo');
                                    }

                                    return [];
                                })
                                ->afterStateUpdated(function (Get $get, Set $set, $state, RelationManager $livewire) {
                                    $info = MantenimientoCorrectivo::where('codigo_equipo', $state)->first();
                                    $set('nro_presupuesto', $info->nro_presupuesto);
                                    $set('monto_presupuesto_usd', $info->monto_presupuesto_usd);
                                })
                                ->searchable()
                                ->live()
                                ->required(),

                            Forms\Components\TextInput::make('nro_presupuesto')
                                ->prefixIcon('heroicon-s-pencil')
                                ->label('Nro. Presupuesto')
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            Forms\Components\TextInput::make('monto_presupuesto_usd')
                                ->prefixIcon('heroicon-s-currency-dollar')
                                ->numeric()
                                ->label('Monto Presupuesto(USD)')
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                        ])
                            ->hidden(function (Get $get) {

                                $mantenimiento = Mantenimiento::where('id', $get('mantenimiento_id'))->first();

                                if (isset($mantenimiento) && $mantenimiento->descripcion == 'correctivo') {
                                    return false;
                                } else {
                                    return true;
                                }
                            }),

                        Grid::make(2)->schema([

                            FileUpload::make('image')
                                ->label('Imagenes de la bitacora')
                                ->image(),

                            FileUpload::make('doc_pdf')
                                ->label('PDF de la bitacora')
                                ->acceptedFileTypes(['application/pdf']),
                        ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('DESCRIPCION DE LAS ACTIVIDADES REALIZADAS')
                    ->description('Detalle de actividades')
                    ->icon('heroicon-m-arrow-trending-down')
                    ->schema([

                        Forms\Components\Textarea::make('trabajo_realizado')
                            ->label('Observaciones Relevante')
                            ->required(),

                    ])
                    ->columnSpanFull(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contrato_id')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Ejecucion')
                    ->dateTime('d-m-Y')
                    ->icon('heroicon-s-check')
                    ->badge()
                    ->color('success'),
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
                Tables\Columns\TextColumn::make('valuacion.descripcion')
                    ->badge()
                    ->color('azul'),

                Tables\Columns\TextColumn::make('trabajo_realizado')
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('monto_presupuesto_usd')
                    ->label('Monto Presupuesto(USD)')
                    ->badge()
                    ->money('USD')
                    ->color('success')
                    ->sortable(),
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