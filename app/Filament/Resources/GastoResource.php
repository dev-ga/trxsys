<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gasto;
use App\Models\Agencia;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Contrato;
use Filament\Forms\Form;
use App\Models\Proveedor;
use App\Models\TipoGasto;
use App\Models\Valuacion;
use App\Models\MetodoPago;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;

use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\GastoResource\Pages;
use App\Http\Controllers\GastoDetalleController;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GastoResource\RelationManagers;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrow-trending-down';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('INFORMACION DEL GASTOS')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-m-arrow-trending-down')
                    ->schema([

                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->prefixIcon('heroicon-c-tag')
                            ->default(function () {
                                if (Gasto::max('id') == null) {
                                    $parte_entera = 0;
                                } else {
                                    $parte_entera = Gasto::max('id');
                                }
                                return '000' . $parte_entera + 1;
                            }),

                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción del gasto')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->maxLength(255),

                        Select::make('tipo_gasto_id')
                            ->label('Tipo de Gasto')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(TipoGasto::all()->pluck('descripcion', 'id'))
                            ->searchable()
                            ->live()
                            ->required(),

                                //Tipo de gasto = contrato
                                //--------------------------------------------------------
                                Select::make('empresa_contratante_id')
                                ->label('Empresa Contratante')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options(EmpresaContratante::all()->pluck('nombre', 'id'))
                                ->searchable()
                                ->required()
                                ->hidden(function (Get $get) {
                                    return $get('tipo_gasto_id') != 1;
                                })
                                ->live(),

                                Select::make('nro_contrato')
                                ->label('Nro. Contrato')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options(function (Get $get) {
                                    return Contrato::where('empresa_contratante_id', $get('empresa_contratante_id'))->pluck('nro_contrato', 'nro_contrato');
                                })
                                ->hidden(function (Get $get) {
                                    return $get('tipo_gasto_id') != 1;
                                })
                                ->required()
                                ->searchable()
                                ->live(),
                                //----------------------------------------------------   ----

                        Forms\Components\Select::make('forma_pago')
                            ->label('Forma de Pago')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->required()
                            ->live()
                            ->options([
                                'dolares' => 'Dolares',
                                'bolivares' => 'Bolivares',
                            ]),

                        Forms\Components\Select::make('metodo_pago_id')
                            ->prefixIcon('heroicon-s-truck')
                            ->label('Método de Pago')
                            ->required()
                            ->options(function (Get $get) {
                                if ($get('forma_pago') == 'dolares') {
                                    return MetodoPago::where('tipo_moneda', 'usd')->pluck('descripcion', 'id');
                                }

                                if ($get('forma_pago') == 'bolivares') {
                                    return MetodoPago::where('tipo_moneda', 'bsd')->pluck('descripcion', 'id');
                                }
                            })
                            ->live(),

                        Forms\Components\TextInput::make('nro_factura')
                            ->label('Nro. Factura/Nota de Entrega')
                            ->prefixIcon('heroicon-c-tag')
                            ->required(),

                        Forms\Components\TextInput::make('nro_control')
                            ->label('Nro. de Control')
                            ->prefixIcon('heroicon-c-tag')
                            ->rules(['required', 'string', 'max:255'])
                            ->validationMessages([
                                'required'  => 'Campo requerido',
                            ]),

                        Forms\Components\Select::make('proveedor_id')
                            ->prefixIcon('heroicon-s-truck')
                            ->relationship('proveedor', 'nombre')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('codigo')
                                    ->default(function () {
                                        if (Proveedor::max('id') == null) {
                                            $parte_entera = 0;
                                        } else {
                                            $parte_entera = Proveedor::max('id');
                                        }
                                        return '000' . $parte_entera + 1;
                                    })
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ci_rif')
                                    ->label('Rif')
                                    ->required(),
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre/Razon Social')
                                    ->required(),
                                Forms\Components\TextInput::make('responsable')
                                    ->prefixIcon('heroicon-s-home')
                                    ->label('Cargado por:')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(Auth::user()->name),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('valuacion_id')
                            ->prefixIcon('heroicon-s-home')
                            ->label('Nro. de Valuación')
                            ->numeric()
                            ->placeholder('Solo numeros enteros, Ejemplo: 1, 2, 3'),

                        Forms\Components\TextInput::make('responsable')
                            ->prefixIcon('heroicon-s-home')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(Auth::user()->name),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Textarea::make('observaciones')
                                    ->label('Observaciones Relevantes'),
                            ]),
                    ])->columns(3),

                Forms\Components\Section::make('COSTOS:')
                    ->description('Formulario de gastos')
                    ->icon('heroicon-c-arrow-trending-down')
                    ->schema([

                        ToggleButtons::make('feedback')
                            ->label('Maneja IVA?')
                            ->boolean()
                            ->inline()
                            ->live()
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->default(false)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('tasa_bcv')
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->live()
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('monto_usd')
                            ->label('Monto en USD($)')
                            ->prefixIcon('heroicon-s-currency-dollar')
                            ->numeric()
                            ->live(onBlur: true)
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'dolares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotales($get, $set);
                            })
                            ->placeholder('0.00'),

                        Forms\Components\TextInput::make('exento')
                            ->label('Exento(Bs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->numeric()
                            ->placeholder('0.00'),

                        Forms\Components\TextInput::make('monto_bsd')
                            ->label('Base imponible(Bs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->hidden(function (Get $get) {
                                if ($get('forma_pago') == 'bolivares') {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->numeric()
                            ->placeholder('0.00')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotales($get, $set);
                            }),


                        Forms\Components\TextInput::make('iva')
                            ->label('IVA(%)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->hidden(function (Get $get) {
                                if ($get('feedback') == true) {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0.00),

                        Forms\Components\TextInput::make('total_gasto_bsd')
                            ->label('Total Gasto en Bolivares(Bs.)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->default(0.00)
                            ->placeholder('0.00'),

                        Forms\Components\TextInput::make('conversion_a_usd')
                            ->label('Total Gasto en Dolares($)')
                            ->prefixIcon('heroicon-m-credit-card')
                            ->live()
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->placeholder('0.00'),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_gasto.descripcion')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('metodo_pago.descripcion')
                    ->label('Método de Pago')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_factura')
                    ->badge()
                    ->color('azul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_control')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->icon('heroicon-s-home')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->label('Nro Contrato')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->label('Monto Bs.')
                    ->alignRight()
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->label('IVA(%)')
                    ->alignRight()
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('conversion_a_usd')
                    ->money('USD')
                    ->alignRight()
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_gasto_bsd')
                    ->label('Total Gasto Bs.')
                    ->alignRight()
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable()
                    ->summarize(Sum::make()
                        ->label('Total(Bs.)')),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->label('Monto USD($)')
                    ->alignRight()
                    ->money('USD')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable()
                    ->summarize(Sum::make()
                    ->label('Total(USD)')),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->numeric()
                    ->alignRight()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('observaciones')
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
            ->actions([
                // Tables\Actions\EditAction::make(),
                ActionGroup::make([

                    Action::make('detalle')
                        ->color('negro')
                        ->icon('heroicon-m-eye')
                        ->model(Gasto::class)
                        ->form([
                            Section::make('Detalle de Gasto')
                                ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                                ->icon('heroicon-c-users')
                                ->schema([
                                    Grid::make()
                                        ->schema([

                                            TextInput::make('nro_factura')
                                                ->label('Nro. Factura')
                                                ->prefixIcon('heroicon-c-users')
                                                ->readOnly()
                                                ->default(function (Gasto $record) {
                                                    return $record->nro_factura;
                                                }),
                                            //codigo de requisicion
                                            TextInput::make('empresa_contratante_id')
                                                ->label('Empresa Contratante')
                                                ->prefixIcon('heroicon-c-users')
                                                ->readOnly()
                                                ->default(function (Gasto $record) {
                                                    return $record->empresaContratante->nombre;
                                                }),

                                            TextInput::make('nro_contrato')
                                                ->label('Nro. Contrato')
                                                ->prefixIcon('heroicon-c-users')
                                                ->readOnly()
                                                ->default(function (Gasto $record) {
                                                    return $record->nro_contrato;
                                                }),
                                        ])->columns(3),
                                ]),

                            Section::make('Productos para requisicion')
                                ->icon('heroicon-c-users')
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            Repeater::make('agencias')
                                                ->schema([
                                                    Grid::make()
                                                        ->schema([
                                                            //Tipo de gasto = contrato
                                                            //--------------------------------------------------------
                                                            Select::make('agencia_id')
                                                            ->label('Agencia')
                                                            ->prefixIcon('heroicon-m-list-bullet')
                                                            ->options(function (Gasto $record, Get $get) {
                                                                return Agencia::where('empresa_contratante_id', $record->empresa_contratante_id)->pluck('nombre', 'id');
                                                            })
                                                            ->searchable()
                                                            ->live(),

                                                            TextInput::make('monto_usd')
                                                                ->label('Monto en Dolares($)')
                                                                ->prefixIcon('heroicon-c-credit-card')
                                                                ->numeric()
                                                                ->placeholder('0.00'),

                                                            TextInput::make('monto_bsd')
                                                                ->label('Monto en Bolivares(Bs.)')
                                                                ->prefixIcon('heroicon-c-credit-card')
                                                                ->numeric()
                                                                ->placeholder('0.00'),
                                                            //--------------------------------------------------------
                                                        ])->columns(3)
                                                ])->columnSpanFull(),
                                        ])->columns(3),
                                ])->collapsible(),
                        ])
                        ->action(function (Gasto $record, array $data) {
                            $detalle = GastoDetalleController::crear_detalle($data, $record);
                            if ($detalle == false) {
                                Notification::make()
                                    ->title('Notificacion')
                                    ->color('success')
                                    ->icon('heroicon-o-shield-check')
                                    ->iconColor('success')
                                    ->body('El detalle fue registrado de forma exitosa')
                                    ->send();
                            }
                        }),

                ])->dropdownPlacement('bottom-start')
                ->size(ActionSize::Small)
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    //Asignar a valuacion
                    Tables\Actions\BulkAction::make('asignacion_valuacion')
                        ->label('Asignar a Valuacion')
                        ->icon('heroicon-o-link')
                        // ->requiresConfirmation()
                        ->color('success')
                        ->form([
                            Section::make('Asignacion Masiva de gastos a valuacion')
                                ->icon('heroicon-s-clipboard-document-list')
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            Select::make('valuacion_id')
                                                ->prefixIcon('heroicon-m-list-bullet')
                                                ->options(Valuacion::all()->pluck('descripcion', 'id'))
                                                ->searchable(),
                                                // ->required(),
                                        ]),
                                ])
                        ])
                        ->action(function (Collection $records, array $data) {
                            // dd($records->with('detalleGastos')->toArray());
                            foreach ($records as $record) {
                                //Actualizar el id de la valuacion en la tabla de gastos
                                $record->valuacion_id = $data['valuacion_id'];
                                $record->save();

                                //Actualizar el id de la valuacion en la tabla de gastos detalles
                                foreach ($record->detalleGastos as $detalle) {
                                    $detalle->valuacion_id = $data['valuacion_id'];
                                    $detalle->save();
                                }
                            }
                        }),

                    //Eliminar
                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }

    //Relacion 1 a N con la tabla de GastoDetalle
    public static function getRelations(): array
    {
        return [
            RelationManagers\DetalleGastosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }

    public static function updateTotales(Get $get, Set $set): void
    {
        $parametro_iva = Configuracion::first()->iva;

        if ($get('feedback') == true && $get('exento') == null) {
            $iva = $get('monto_bsd') * $parametro_iva;
            $set('iva', round($iva, 2));
            $set('total_gasto_bsd',  round(($get('monto_bsd') + $iva), 2));
            $set('conversion_a_usd', round($get('total_gasto_bsd') / $get('tasa_bcv'), 2));
        }

        if ($get('feedback') == true && $get('exento') != null) {
            $iva = $get('monto_bsd') * $parametro_iva;
            $set('iva', round($iva, 2));
            $set('total_gasto_bsd',  round(($get('monto_bsd') + $iva + $get('exento')), 2));
            $set('conversion_a_usd', round($get('total_gasto_bsd') / $get('tasa_bcv'), 2));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'dolares') {
            $set('conversion_a_usd', round($get('monto_usd'), 2));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'bolivares') {
            $set('total_gasto_bsd',  round($get('monto_bsd'), 2));
            $set('conversion_a_usd', round($get('monto_bsd') / $get('tasa_bcv'), 2));
        }
    }

    // public static function updateMontoBsd(Get $get, Set $set): void
    // {
    //     $parametro_bcv = Configuracion::first()->tasa_bcv;
    //     $monto_bsd = $get('monto_usd') * $parametro_bcv;
    //     $set('monto_bsd', round($monto_bsd, 2));
    // }
}
