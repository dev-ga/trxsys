<?php

namespace App\Filament\Resources\ContratoResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Equipo;
use App\Models\Agencia;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Valuacion;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
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
                Section::make('Equipo')
                    ->description('Formulario para la carga de equipos. Campos Requeridos(*)')
                    ->icon('heroicon-o-server-stack')
                    ->schema([
                        Section::make('Equipo')
                            ->description('Informacion principal. Campos Requeridos(*)')
                            ->schema([

                                Forms\Components\Select::make('agencia_id')
                                    ->label('Agencia')
                                    ->relationship('agencia', 'nombre')
                                    ->options(function (RelationManager $livewire) {
                                        return Agencia::where('contrato_id', $livewire->ownerRecord->id)->pluck('nombre', 'id');
                                    })
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $codigo_agencia = Agencia::where('id', $get('agencia_id'))->first();
                                        if(!$codigo_agencia){
                                            $set('codigo', null);
                                            return;
                                            
                                        }else{
                                            $codigo = 'TRX-' . $codigo_agencia->codigo . '-' . rand(11111, 99999);
                                            $set('codigo', $codigo);
                                            
                                        }
                                    })
                                    ->preload()
                                    ->searchable()
                                    ->live()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('codigo')
                                    ->prefixIcon('heroicon-s-pencil')
                                    ->label('Código de equipo')
                                    ->live()
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('area_suministro')
                                    ->prefixIcon('heroicon-s-pencil')
                                    ->label('Area de Suministro'),
                                Forms\Components\TextInput::make('responsable')
                                    ->prefixIcon('heroicon-c-user-circle')
                                    ->label('Cargado por:')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(Auth::user()->name),
                            ])->columns(2),

                Section::make('CONDENSADORA')
                    ->description('Caracteristicas de la condensadora. Campos Requeridos(*)')
                    ->schema([
                        Forms\Components\TextInput::make('toneladas')

                            ->numeric()
                            ->live()
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


                        Forms\Components\Select::make('voltaje')

                            ->label('Voltaje')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                '110v'  => '110v',
                                '220v'  => '220v',
                                '440v'  => '440v',
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('motor_ventilador_hp')

                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Motor Ventilador(Hp)'),

                        Forms\Components\TextInput::make('motor_ventilador_eje')

                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Motor Ventilador(Eje)'),

                        Forms\Components\TextInput::make('tipo_correa')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Tipo de correa'),
                        Forms\Components\TextInput::make('rpm')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('RPM'),
                    ])->columns(2),

                Section::make('EVAPORADORA')
                    ->description('Caracteristicas de la evaporadora. Campos Requeridos(*)')
                    ->schema([
                        Forms\Components\TextInput::make('toneladas_eva')

                            ->numeric()
                            ->live()
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Toneladas'),

                        Forms\Components\Select::make('ph_eva')

                            ->label('PH(Phase)')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                            ])
                            ->searchable(),

                        Forms\Components\Select::make('refrigerante_eva')

                            ->label('Refrigerante')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                'R-22'  => 'R-22',
                                'R-410' => 'R-410',
                            ])
                            ->searchable(),


                        Forms\Components\Select::make('voltaje_eva')

                            ->label('Voltaje')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                '110v'  => '110v',
                                '220v'  => '220v',
                                '440v'  => '440v',
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('motor_ventilador_hp_eva')

                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Motor Ventilador(Hp)'),

                        Forms\Components\TextInput::make('motor_ventilador_eje_eva')

                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Motor Ventilador(Eje)'),

                        Forms\Components\TextInput::make('tipo_correa_eva')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Tipo de correa'),
                        Forms\Components\TextInput::make('rpm_eva')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('RPM'),
                    ])->columns(2),

                        Section::make('Fotos')
                            ->description('Fotos del equipo')
                            ->schema([
                                FileUpload::make('image_placa_condensadora')
                                    ->label('Foto placa condensadora')
                                    ->image()
                                    ->imageEditor(),

                                FileUpload::make('image_placa_ventilador')
                                    ->label('Foto placa ventilador')
                                    ->image()
                                    ->imageEditor(),
                            ])->columns(2),
                    ])
            ]);
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contrato_id')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Equipo')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agencia.nombre')
                ->searchable()
                ->badge()
                ->color('naranja')
                ->icon('heroicon-s-home')
                ->label('Agencia'),
                Tables\Columns\TextColumn::make('toneladas')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->badge()
                    ->color('marronClaro')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area_suministro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PH')
                    ->label('PH')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refrigerante')
                    ->searchable(),

                Tables\Columns\TextColumn::make('voltaje')
                    ->searchable(),

                Tables\Columns\TextColumn::make('motor_ventilador_hp')
                    ->label('Motor Ventilador(Hp)')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('motor_ventilador_eje')
                    ->label('Motor Ventilador(Eje)')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_correa')
                    ->label('Tipo Correa')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                ImageColumn::make('image_placa_condensadora')
                    ->size(60)
                    ->square(),
                ImageColumn::make('image_placa_ventilador')
                    ->size(60)
                    ->square(),
                Tables\Columns\TextColumn::make('responsable')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y')
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

                                    Forms\Components\Select::make('valuacion_id')
                                        ->label('Valuacion')
                                        ->options(function (RelationManager $livewire) {
                                            return Valuacion::where('contrato_id', $livewire->ownerRecord->id)
                                                ->where('mantenimiento_id', 1)
                                                ->get()->pluck('descripcion', 'id');
                                        })
                                        ->searchable()
                                        ->required()
                                        ->preload(),

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

                        try {

                            // DB::transaction(function () use ($records, $data) {});
                            // DB::rollBack();
                            foreach ($records as $record) {
                                //hacemos el registro en la tabla de mantenimiento_preventivos
                                $mantenimiento = new \App\Models\MantenimientoPreventivo();
                                $mantenimiento->equipo_id = $record->id;
                                $mantenimiento->agencia_id = $record->agencia_id;
                                $mantenimiento->contrato_id = $record->contrato_id;
                                $mantenimiento->valuacion_id = $data['valuacion_id'];
                                $mantenimiento->codigo_equipo = $record->codigo;
                                $mantenimiento->toneladas = $record->toneladas;
                                $mantenimiento->calculo_x_tonelada = $record->toneladas * Configuracion::all()->first()->costo_tonelada_usd;;
                                $mantenimiento->fecha_ejecucion = $data['fecha_ejecucion'] == null ? Carbon::now()->format('d-m-Y') : $data['fecha_ejecucion'];
                                $mantenimiento->fecha_prox_ejecucion = $data['fecha_prox_ejecucion'] == null ? Carbon::now()->addMonths(Configuracion::all()->first()->tiempo_x_mantenimiento_prev)->format('d-m-Y') : $data['fecha_prox_ejecucion'];
                                $mantenimiento->responsable = Auth::user()->name;
                                $mantenimiento->save();

                                $valuacion = Valuacion::where('id', $data['valuacion_id'])
                                    ->where('contrato_id', $record->contrato_id)
                                    ->first();

                                $valuacion->monto_usd = $valuacion->monto_usd + $mantenimiento->calculo_x_tonelada;
                                $valuacion->save();
                            }

                            Notification::make()
                                ->title('Notificacion')
                                ->color('success')
                                ->icon('heroicon-o-shield-check')
                                ->iconColor('danger')
                                ->body('Asignacion masiva de MP exitosa. Se actualizo el monto de la valuacion')
                                ->send();
 
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('Notificacion')
                                ->color('danger')
                                ->body($th->getMessage())
                                ->success()
                                ->send();
                        }
                    }),
                ]),
            ]);
    }
}