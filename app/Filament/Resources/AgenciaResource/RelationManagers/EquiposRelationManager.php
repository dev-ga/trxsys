<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Agencia;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EquiposRelationManager extends RelationManager
{
    protected static string $relationship = 'equipos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('ASOCIACION DE EQUIPOS')
                ->description('Formulario para la carga de equipos por agencia.')
                ->icon('heroicon-m-arrow-trending-down')
                ->schema([

                    Forms\Components\TextInput::make('codigo')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Codigo')
                        ->default(function (RelationManager $livewire) {
                            $codigo = 'TRX-' . $livewire->ownerRecord->codigo . '-' . rand(11111, 99999);
                            return $codigo;    
                        })
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\TextInput::make('toneladas')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Toneladas'),

                    Forms\Components\Select::make('PH')
                        ->label('PH(Phase)')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options([
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                        ])
                        ->searchable(),
 
                    Forms\Components\Select::make('refrigerante')
                        ->label('Refrigerante')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options([
                            'R-22'  => 'R-22',
                            'R-410' => 'R-410',
                        ])
                        ->searchable(),
                    Forms\Components\TextInput::make('area_suministro')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Area de Suministro'),
                    Forms\Components\Select::make('voltaje')
                        ->label('Voltaje')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options([
                            '110v'  => '110v',
                            '220v'  => '220v',
                            '440v'  => '440v',
                        ])
                        ->searchable(),
                    Forms\Components\TextInput::make('responsable')
                        ->prefixIcon('heroicon-c-user-circle')
                        ->label('Cargado por:')
                        ->disabled()
                        ->dehydrated()
                        ->default(Auth::user()->name),


                ])
                ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('agencia_id')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agencia.nombre')
                    ->icon('heroicon-s-home')
                    ->sortable(),
                Tables\Columns\TextColumn::make('toneladas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PH')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refrigerante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area_suministro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('voltaje')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsable')
                ->icon('heroicon-c-user-circle')
                ->label('Cargado por:'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    //                     Forms\Components\Select::make('agencia_id')
                    //                     ->label('Agencia')
                    //                     ->relationship('agencia', 'nombre')
                    //                     ->preload()
                    //                     ->default(function (RelationManager $livewire) {
                    //                         return $livewire->ownerRecord->id;
                    //                     })
                    //                     ->disabled()
                    //                     ->dehydrated(),

                    //                 Forms\Components\Select::make('equipo_id')
                    //                     ->label('Equipo')
                    //                     ->relationship('equipos', 'codigo')
                    //                     ->preload(),
                    //                     // ->default(function (RelationManager $livewire) {
                    //                     //     return $livewire->ownerRecord->id;
                    //                     // })
                    //                     // ->disabled()
                    //                     // ->dehydrated(),


                    //                 Forms\Components\TextInput::make('codigo_equipo')
                    //                     ->prefixIcon('heroicon-s-pencil')
                    //                     ->label('Codigo')
                    //                     ->default(function (RelationManager $livewire) {
                    //                         return $livewire->ownerRecord->codigo;
                    //                     })
                    //                     ->disabled()
                    //                     ->dehydrated(),

                    //                 Forms\Components\TextInput::make('toneladas')
                    //                     ->prefixIcon('heroicon-s-pencil')
                    //                     ->label('Toneladas')
                    //                     ->default(function (RelationManager $livewire) {
                    //                         $equipo = Equipo::where('agencia_id', $livewire->ownerRecord->id)->first();
                    //                         return $equipo->toneladas;
                    //                     })
                    //                     ->disabled()
                    //                     ->dehydrated(),

                    //                 Forms\Components\TextInput::make('calculo_x_tonelada')
                    //                     ->prefixIcon('heroicon-s-currency-dollar')
                    //                     ->numeric()
                    //                     ->label('Costo por Tonelada(USD)')
                    //                     ->default(function (Get $get, RelationManager $livewire) {
                    //                         $tonelada = $get('toneladas');
                    //                         $costo = Configuracion::all()->first()->costo_tonelada_usd;
                    //                         return $tonelada * $costo;
                    //                     })
                    //                     ->disabled()
                    //                     ->dehydrated(),

                    //                 Forms\Components\DatePicker::make('fecha_ejecucion')
                    //                     ->prefixIcon('heroicon-c-calendar-date-range')
                    //                     ->label('Fecha Mantenimiento')
                    //                     ->displayFormat('d-m-Y')
                    //                     ->required()
                    //                     ->afterStateUpdated(function (Get $get, Set $set) {
                    //                         $tiempo_x_mantenimiento = Configuracion::all()->first()->tiempo_x_mantenimiento_prev;
                    //                         $prox_fecha = Carbon::parse($get('fecha_ejecucion'))->addMonths($tiempo_x_mantenimiento)->format('d-m-Y');
                    //                         $set('fecha_prox_ejecucion', $prox_fecha);
                    //                     })
                    //                     ->live(),

                    //                 Forms\Components\TextInput::make('fecha_prox_ejecucion')
                    //                     ->prefixIcon('heroicon-c-calendar-date-range')
                    //                     ->label('Proximo Mantenimiento')
                    //                     ->disabled()
                    //                     ->dehydrated(),

                    //                 Forms\Components\TextInput::make('responsable')
                    //                     ->prefixIcon('heroicon-c-user-circle')
                    //                     ->label('Cargado por:')
                    //                     ->disabled()
                    //                     ->dehydrated()
                    //                     ->default(Auth::user()->name),
                    // ]),
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

    protected function afterSave(): void
    {
        dd($this->getOwnerRecord());
        $this->record->equipos()->syncWithoutDetaching([$this->options['categoryId']]);
    }
}