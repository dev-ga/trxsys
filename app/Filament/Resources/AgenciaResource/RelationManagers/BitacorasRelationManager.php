<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Contrato;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Mantenimiento;
use App\Models\EmpresaContratante;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Models\MantenimientoCorrectivo;
use App\Models\MantenimientoPreventivo;
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
                        
                        Select::make('empresa_contratante_id')
                            ->label('Empresa Contratante')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(EmpresaContratante::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->required()
                            ->live(),

                        Select::make('nro_contrato')
                            ->label('Nro. Contrato')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(function (Get $get) {
                                return Contrato::where('empresa_contratante_id', $get('empresa_contratante_id'))->pluck('nro_contrato', 'nro_contrato');
                            })
                            ->required()
                            ->searchable()
                            ->live(),

                    ]),

                    Grid::make(2)->schema([

                        Forms\Components\TextInput::make('valuacion_id')
                            ->prefixIcon('heroicon-s-home')
                            ->label('Nro. Valuacion')
                            ->hint('Numero enteros')
                            ->placeholder('Ejemplo: 1, 2, 3'),
                            
                        Forms\Components\Select::make('mantenimiento_id')
                            ->label('Tipo de Mantenimiento')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(Mantenimiento::all()->pluck('descripcion', 'id'))
                            ->searchable()
                            ->required()
                            ->live(),
                    ]),

                    Grid::make(3)->schema([

                        Forms\Components\Select::make('equipo_id')
                            ->label('Tipo de Mantenimiento')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(function (Get $get,RelationManager $livewire) {
                                
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
                        }else{
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

                

                    Forms\Components\TextInput::make('responsable')
                        ->prefixIcon('heroicon-s-home')
                        ->label('Cargado por:')
                        ->disabled()
                        ->dehydrated()
                        ->default(Auth::user()->name),
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
            ->recordTitleAttribute('agencia_id')
            ->columns([

                Tables\Columns\TextColumn::make('agencia.nombre')
                ->searchable()
                ->icon('heroicon-s-home')
                ->label('Agencia'),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                ->searchable()
                ->label('Empresa Contratante')
                ->badge()
                ->color('marronClaro'),
                Tables\Columns\TextColumn::make('nro_contrato')
                ->searchable()
                ->label('Nro. Contrato')
                ->badge()
                ->color('marronClaro'),
                // Tables\Columns\TextColumn::make('image'),
                // Tables\Columns\TextColumn::make('doc_pdf'),
                Tables\Columns\TextColumn::make('mantenimiento.descripcion')
                ->searchable()
                ->label('Tipo de Mantenimiento')
                ->badge()
                ->color(function ($state) {
                    return match ($state) {
                        'preventivo' => 'marronClaro',
                        'correctivo' => 'success',
                        default      => 'marronClaro',
                    };
                })
                ->icon(function ($state) {
                    return match ($state) {
                        'preventivo' => 'heroicon-m-shield-exclamation',
                        'correctivo' => 'heroicon-m-shield-check',
                        default      => 'heroicon-s-wrench',
                    };
                }),
                Tables\Columns\TextColumn::make('valuacion.descripcion'),
                Tables\Columns\TextColumn::make('responsable'),
                Tables\Columns\TextColumn::make('trabajo_realizado'),
                Tables\Columns\TextColumn::make('nro_presupuesto')
                    ->label('Nro. Presupuesto')
                    ->badge()
                    ->color('marronClaro')
                    ->sortable(),
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
                Tables\Actions\EditAction::make()
                ->color('naranja'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}