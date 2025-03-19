<?php

namespace App\Filament\Resources\ContratoResource\RelationManagers;

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
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use App\Models\MantenimientoCorrectivo;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MantenimientoCorrectivosRelationManager extends RelationManager
{
    protected static string $relationship = 'mantenimientoCorrectivos';

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
                            ->options(function (RelationManager $livewire) {
                                return Agencia::where('contrato_id', $livewire->ownerRecord->id)->get()->pluck('nombre', 'id');
                            })
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('equipo_id')
                            ->label('Equipo')
                            ->options(function (Get $get) {
                                return Equipo::where('agencia_id', $get('agencia_id'))->get()->pluck('codigo', 'id');
                            })
                            ->searchable()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {

                                $codigo = Equipo::where('agencia_id', $get('agencia_id'))
                                    ->where('id', $get('equipo_id'))
                                    ->first();

                                if (!$codigo) {
                                    $set('codigo_equipo', null);
                                    $set('toneladas', null);
                                    $set('calculo_x_tonelada', null);
                                    return;
                                } else {
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


                Forms\Components\TextInput::make('nro_presupuesto')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Nro. Presupuesto')
                            ->required(),

                        Forms\Components\TextInput::make('monto_presupuesto_usd')
                            ->prefixIcon('heroicon-s-currency-dollar')
                            ->numeric()
                            ->label('Monto Presupuesto(USD)')
                            ->required(),

                        Forms\Components\TextInput::make('responsable')
                            ->prefixIcon('heroicon-c-user-circle')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(Auth::user()->name),

                        Grid::make('columnSpanFull')->schema([
                            Forms\Components\Textarea::make('detalles')
                                ->label('Detalles de la actividad')
                                ->placeholder('Escriba aqui la informacion detalla de la actividad')
                                ->required(),

                        ]),

                        Grid::make('columnSpanFull')->schema([
                            FileUpload::make('doc_pdf')
                                ->label('Adjuntar Documento')
                                // ->acceptedFileTypes(['application/pdf'])
                                ->required(),

                        ])

                    ])->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contrato_id')
            ->columns([
                Tables\Columns\TextColumn::make('equipo_id')
                    ->label('ID')
                    ->badge()
                    ->color('naranja')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agencia.nombre')
                ->label('Agencia')
                ->icon('heroicon-s-home')
                ->searchable(),
                Tables\Columns\TextColumn::make('valuacion_id')
                    ->badge()
                    ->color('marronClaro'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Ejecucion')
                    ->dateTime('d-m-Y')
                    ->icon('heroicon-s-check')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('codigo_equipo')
                    ->badge()
                    ->color('marronClaro'),
                Tables\Columns\TextColumn::make('nro_presupuesto')
                    ->label('Nro. Presupuesto')
                    ->badge()
                    ->color('marronClaro')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_presupuesto_usd')
                    ->label('Monto Ejecutado(USD)')
                    ->badge()
                    ->money('USD')
                    ->color('success')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->after(function (MantenimientoCorrectivo $record) {

                    try {

                        //aumentamos el valor de la valuacion
                        $valuacion = Valuacion::where('id', $record->valuacion_id)
                            ->where('contrato_id', $record->contrato_id)
                            ->first();

                        $valuacion->monto_usd = $valuacion->monto_usd + $record->monto_presupuesto_usd;
                        $valuacion->save();

                        if ($valuacion->save()) {

                            Notification::make()
                                ->title('Notificacion')
                                ->color('success')
                                ->icon('heroicon-o-shield-check')
                                ->iconColor('danger')
                                ->body('Valuacion Actualizada de forma correcta')
                                ->send();
                        }
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('Notificacion')
                            ->color('danger')
                            ->icon('heroicon-o-shield-check')
                            ->iconColor('danger')
                            ->body($th->getMessage())
                            ->send();
                    }
                })
                ->createAnother(false),
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