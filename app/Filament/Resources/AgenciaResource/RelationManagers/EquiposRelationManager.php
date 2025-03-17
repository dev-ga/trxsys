<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Agencia;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
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
                    ->label('PH(Phase)')
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
                    ->dateTime('d-m-Y')
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
                    Tables\Actions\DeleteBulkAction::make()
                    ->color('danger'),
                //Asignar a valuacion
                Tables\Actions\BulkAction::make('masivo_mp')
                    ->label('Crear MP')
                    ->icon('heroicon-o-link')
                    // ->requiresConfirmation()
                    ->color('naranja')
                    ->form([
                        Section::make('Asignacion Masiva de gastos a valuacion')
                            ->icon('heroicon-s-clipboard-document-list')
                            ->schema([
                                Grid::make()
                                    ->schema([

                                        Forms\Components\DatePicker::make('fecha_ejecucion')
                                            ->prefixIcon('heroicon-c-calendar-date-range')
                                            ->label('Fecha Mantenimiento')
                                            ->displayFormat('d-m-Y')
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
                                    ]),
                            ])
                    ])
                    ->action(function (Collection $records, array $data) {

                        foreach ($records as $record) {
                            //hacemos el registro en la tabla de mantenimiento_preventivos
                            $mantenimiento = new \App\Models\MantenimientoPreventivo();
                            $mantenimiento->equipo_id = $record->id;
                            $mantenimiento->agencia_id = $record->agencia_id;
                            $mantenimiento->codigo_equipo = $record->codigo;
                            $mantenimiento->toneladas = $record->toneladas;
                            $mantenimiento->calculo_x_tonelada = $record->toneladas * Configuracion::all()->first()->costo_tonelada_usd;;
                            $mantenimiento->fecha_ejecucion = $data['fecha_ejecucion'] == null ? Carbon::now()->format('d-m-Y') : $data['fecha_ejecucion'];
                            $mantenimiento->fecha_prox_ejecucion = $data['fecha_prox_ejecucion'] == null ? Carbon::now()->addMonths(Configuracion::all()->first()->tiempo_x_mantenimiento_prev)->format('d-m-Y') : $data['fecha_prox_ejecucion'];
                            $mantenimiento->responsable = Auth::user()->name;
                            $mantenimiento->save();

                            if($mantenimiento->save()){
                                Notification::make()
                                ->title('Notificacion')
                                ->color('success')
                                ->body('Se han creado los mantenimiento preventivo exitosamente!')
                                ->success()
                                ->send();
                            }

                        }
                    }),
                ]),
            ]);
    }

    protected function afterSave(): void
    {
        dd($this->getOwnerRecord());
        $this->record->equipos()->syncWithoutDetaching([$this->options['categoryId']]);
    }
}
