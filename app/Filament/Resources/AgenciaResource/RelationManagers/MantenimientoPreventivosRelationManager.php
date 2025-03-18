<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Equipo;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Illuminate\Support\Collection;
use App\Models\ValuacionPreventivo;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MantenimientoPreventivosRelationManager extends RelationManager
{
    protected static string $relationship = 'MantenimientoPreventivos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Equipo')
                    ->description('Formulario para la carga de mantenimientos preventivos. Campos Requeridos(*)')
                    ->icon('heroicon-m-server-stack')
                    ->schema([

                        Forms\Components\Select::make('agencia_id')
                            ->label('Agencia')
                            ->relationship('agencia', 'nombre')
                            ->preload()
                            ->default(function (RelationManager $livewire) {
                                return $livewire->ownerRecord->id;
                            })
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('equipo_id')
                            ->label('Equipo')
                            ->options(function (RelationManager $livewire) {
                                return Equipo::where('agencia_id', $livewire->ownerRecord->id)->get()->pluck('codigo', 'id');
                            })
                            ->searchable()
                            ->live(onBlur: true)
                             ->afterStateUpdated(function (Get $get, Set $set, RelationManager $livewire) {
                                
                                $codigo = Equipo::where('agencia_id', $livewire->ownerRecord->id)
                                ->where('id', $get('equipo_id'))
                                ->first();

                                if (!$codigo) {
                                    $set('codigo_equipo', null);
                                    $set('toneladas', null);
                                    $set('calculo_x_tonelada', null);
                                    return;
                                    
                                }else{
                                    $set('codigo_equipo', $codigo->codigo);
                                    $set('toneladas', $codigo->toneladas);
    
                                    $costo = Configuracion::all()->first()->costo_tonelada_usd;
                                    $set('calculo_x_tonelada', $codigo->toneladas * $costo);
                                    
                                }
                                
                            })
                            ->preload(),


                        Forms\Components\TextInput::make('codigo_equipo')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Codigo'),

                        Forms\Components\Select::make('valuacion_preventivo_id')
                            ->label('Valuacion')
                            ->options(function (RelationManager $livewire) {
                                return ValuacionPreventivo::all()->pluck('descripcion', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->preload(),

                        Forms\Components\TextInput::make('toneladas')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Toneladas')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('calculo_x_tonelada')
                            ->prefixIcon('heroicon-s-currency-dollar')
                            ->numeric()
                            ->label('Costo por Tonelada(USD)')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('fecha_ejecucion')
                            ->prefixIcon('heroicon-c-calendar-date-range')
                            ->label('Fecha Mantenimiento')
                            ->displayFormat('d-m-Y')
                            ->required()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $tiempo_x_mantenimiento = Configuracion::all()->first()->tiempo_x_mantenimiento_prev;
                                $prox_fecha = Carbon::parse($get('fecha_ejecucion'))->addMonths($tiempo_x_mantenimiento)->format('d-m-Y');
                                $set('fecha_prox_ejecucion', $prox_fecha);
                            })
                            ->live(),

                        Forms\Components\TextInput::make('fecha_prox_ejecucion')
                            ->prefixIcon('heroicon-c-calendar-date-range')
                            ->label('Proximo Mantenimiento')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('responsable')
                            ->prefixIcon('heroicon-c-user-circle')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(Auth::user()->name),

                    ])->columns(3),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('agencia_id')
            ->columns([
            // Tables\Columns\TextColumn::make('equipo_id')
            //     ->label('ID')
            //     ->badge()
            //     ->color('naranja')
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('agencia.nombre')
            //     ->searchable()
            //     ->icon('heroicon-s-home')
            //     ->label('Agencia'),
            Tables\Columns\TextColumn::make('valuacion_preventivo_id')
                ->badge()
                ->color('marronClaro'),
            Tables\Columns\TextColumn::make('codigo_equipo')
                ->badge()
                ->color('marronClaro')
                ->searchable(),
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
                ->icon('heroicon-s-calendar-date-range')
                ->badge()
                ->color('marronClaro')
                ->dateTime('d-m-Y'),
            Tables\Columns\TextColumn::make('fecha_prox_ejecucion')
                ->label('Proximo Mantenimiento')
                ->icon('heroicon-m-wrench')
                ->badge()
                ->color('naranja')
                ->dateTime('d-m-Y'),
            // Tables\Columns\TextColumn::make('responsable')
            //     ->icon('heroicon-c-user-circle')
            //     ->label('Cargado por:'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                //Asignar a valuacion
                    // Tables\Actions\BulkAction::make('asignacion_valuacion')
                    // ->label('Asignar a Valuacion')
                    // ->icon('heroicon-o-link')
                    // // ->requiresConfirmation()
                    // ->color('success')
                    // ->form([
                    //     Section::make('Asignacion Masiva de gastos a valuacion')
                    //         ->icon('heroicon-s-clipboard-document-list')
                    //         ->schema([
                    //             Grid::make()
                    //                 ->schema([
                    //                     Forms\Components\DatePicker::make('fecha_ejecucion')
                    //                         ->prefixIcon('heroicon-c-calendar-date-range')
                    //                         ->label('Fecha Mantenimiento')
                    //                         ->displayFormat('d-m-Y')
                    //                         ->required()
                    //                         ->afterStateUpdated(function (Get $get, Set $set) {
                    //                             $tiempo_x_mantenimiento = Configuracion::all()->first()->tiempo_x_mantenimiento_prev;
                    //                             $prox_fecha = Carbon::parse($get('fecha_ejecucion'))->addMonths($tiempo_x_mantenimiento)->format('d-m-Y');
                    //                             $set('fecha_prox_ejecucion', $prox_fecha);
                    //                         })
                    //                         ->live(),

                    //                     Forms\Components\TextInput::make('fecha_prox_ejecucion')
                    //                         ->prefixIcon('heroicon-c-calendar-date-range')
                    //                         ->label('Proximo Mantenimiento')
                    //                         ->disabled()
                    //                         ->dehydrated(),
                    //                 ]),
                    //         ])
                    // ])
                    // ->action(function (Collection $records, array $data) {
                    //     // dd($records->with('detalleGastos')->toArray());
                    //     foreach ($records as $record) {
                    //         //Actualizar el id de la valuacion en la tabla de gastos
                    //         $record->valuacion_id = $data['valuacion_id'];
                    //         $record->save();

                    //         //Actualizar el id de la valuacion en la tabla de gastos detalles
                    //         foreach ($record->detalleGastos as $detalle) {
                    //             $detalle->valuacion_id = $data['valuacion_id'];
                    //             $detalle->save();
                    //         }
                    //     }
                    // }),
                ]),
            ]);
    }
}