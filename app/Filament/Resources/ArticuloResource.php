<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Almacen;
use App\Models\Articulo;
use Filament\Forms\Form;
use App\Models\Categoria;
use App\Models\Inventario;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Http\Controllers\InventarioController;
use App\Filament\Resources\ArticuloResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Http\Controllers\InventarioMovimientoController;
use App\Filament\Resources\ArticuloResource\RelationManagers;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Articulo')
                    ->description('Formulario para la carga de articulos. Campos Requeridos(*)')
                    ->icon('heroicon-o-view-columns')
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->prefixIcon('heroicon-c-tag')
                            ->default(function () {
                                if (Articulo::max('id') == null) {
                                    $parte_entera = 0;
                                } else {
                                    $parte_entera = Articulo::max('id');
                                }
                                return 'TRX-ART-000' . $parte_entera + 1;
                            })
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->maxLength(255),
                        //select Categoprias
                        Select::make('categoria_id')
                            ->label('Categoría del Articulo')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(function () {
                                return Categoria::all()->pluck('descripcion', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('precio_unitario')
                            ->required()
                            ->numeric()
                            ->placeholder('0.00'),
                        Forms\Components\TextInput::make('responsable')
                            ->prefixIcon('heroicon-c-user-circle')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(Auth::user()->name),

                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio_unitario')
                    ->money('USD')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable(),
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
                ActionGroup::make([

                    Action::make('entrada')
                        ->label('Entrada Inventario')
                        ->color('negro')
                        ->icon('heroicon-o-arrow-turn-down-right')
                        ->model(Articulo::class)
                        ->form([
                            Section::make('Entrada de Inventario')
                                ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                                ->icon('heroicon-o-arrow-turn-down-right')
                                ->schema([
                                    Grid::make()
                                        ->schema([

                                            Forms\Components\TextInput::make('codigo')
                                                ->label('Codigo')
                                                ->prefixIcon('heroicon-c-tag')
                                                ->default(function () {
                                                    if (Inventario::max('id') == null) {
                                                        $parte_entera = 0;
                                                    } else {
                                                        $parte_entera = Inventario::max('id');
                                                    }
                                                    return 'TRX-ART-000' . $parte_entera + 1;
                                                }),

                                            Select::make('almacen_id')
                                                ->label('Almacen')
                                                ->prefixIcon('heroicon-m-list-bullet')
                                                ->options(Almacen::all()->pluck('descripcion', 'id'))
                                                ->searchable()
                                                ->live()
                                                ->required(),

                                                Grid::make(3)->schema([
                                                    Forms\Components\TextInput::make('cantidad')
                                                        ->label('Cantida entrante')
                                                        ->prefixIcon('heroicon-c-tag')
                                                        ->required(),

                                                    Forms\Components\TextInput::make('nro_factura')
                                                        ->label('Nro. Factura/Nota de Entrega')
                                                        ->prefixIcon('heroicon-c-tag'),

                                                    Forms\Components\TextInput::make('responsable')
                                                        ->prefixIcon('heroicon-s-home')
                                                        ->label('Cargado por:')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->default(Auth::user()->name),

                                                ])


                                        ])->columns(2),
                                ]),
                        ])
                        ->action(function (Articulo $record, array $data) {
                            //entrada al inventario
                            $entrada = InventarioController::entrada($data, $record);

                            //carga del asiento para el movimiento del inventario
                        }),


                    // Action::make('reposicion')
                    //     ->label('Reposicion Inventario')
                    //     ->color('negro')
                    //     ->icon('heroicon-o-arrow-turn-down-right')
                    //     ->model(Articulo::class)
                    //     ->form([
                    //         Section::make('Entrada de Inventario')
                    //             ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                    //             ->icon('heroicon-o-arrow-turn-down-right')
                    //             ->schema([
                    //                 Grid::make()
                    //                     ->schema([

                    //                         Forms\Components\TextInput::make('codigo')
                    //                             ->label('Codigo')
                    //                             ->prefixIcon('heroicon-c-tag')
                    //                             ->default(function () {
                    //                                 if (InventarioMovimiento::max('id') == null) {
                    //                                     $parte_entera = 0;
                    //                                 } else {
                    //                                     $parte_entera = InventarioMovimiento::max('id');
                    //                                 }
                    //                                 return 'TRX-MOV-000' . $parte_entera + 1;
                    //                             }),

                    //                         Select::make('almacen_id')
                    //                             ->label('Almacen')
                    //                             ->prefixIcon('heroicon-m-list-bullet')
                    //                             ->options(Almacen::all()->pluck('descripcion', 'id'))
                    //                             ->searchable()
                    //                             ->live()
                    //                             ->required(),

                    //                         Grid::make(3)->schema([
                    //                             Forms\Components\TextInput::make('cantidad')
                    //                                 ->label('Cantida entrante')
                    //                                 ->prefixIcon('heroicon-c-tag')
                    //                                 ->required(),

                    //                             Forms\Components\TextInput::make('nro_factura')
                    //                                 ->label('Nro. Factura/Nota de Entrega')
                    //                                 ->prefixIcon('heroicon-c-tag'),

                    //                             Forms\Components\TextInput::make('responsable')
                    //                                 ->prefixIcon('heroicon-s-home')
                    //                                 ->label('Cargado por:')
                    //                                 ->disabled()
                    //                                 ->dehydrated()
                    //                                 ->default(Auth::user()->name),

                    //                         ])


                    //                     ])->columns(2),
                    //             ]),
                    //     ])
                    //     ->action(function (Articulo $record, array $data) {
                    //         //entrada al inventario
                    //         $entrada = InventarioController::entrada($data, $record);

                    //         //carga del asiento para el movimiento del inventario
                    //     }),

                    // Action::make('salida')
                    //     ->label('Salida Inventario')
                    //     ->color('negro')
                    //     ->icon('heroicon-o-arrow-turn-down-right')
                    //     ->model(Articulo::class)
                    //     ->form([
                    //         Section::make('Entrada de Inventario')
                    //             ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                    //             ->icon('heroicon-o-arrow-turn-down-right')
                    //             ->schema([
                    //                 Grid::make()
                    //                     ->schema([

                    //                         Forms\Components\TextInput::make('codigo')
                    //                             ->label('Codigo')
                    //                             ->prefixIcon('heroicon-c-tag')
                    //                             ->default(function () {
                    //                                 if (InventarioMovimiento::max('id') == null) {
                    //                                     $parte_entera = 0;
                    //                                 } else {
                    //                                     $parte_entera = InventarioMovimiento::max('id');
                    //                                 }
                    //                                 return 'TRX-MOV-000' . $parte_entera + 1;
                    //                             }),

                    //                         Select::make('almacen_id')
                    //                             ->label('Almacen')
                    //                             ->prefixIcon('heroicon-m-list-bullet')
                    //                             ->options(Almacen::all()->pluck('descripcion', 'id'))
                    //                             ->searchable()
                    //                             ->live()
                    //                             ->required(),

                    //                         Grid::make(3)->schema([
                    //                             Forms\Components\TextInput::make('cantidad')
                    //                                 ->label('Cantida entrante')
                    //                                 ->prefixIcon('heroicon-c-tag')
                    //                                 ->required(),

                    //                             Forms\Components\TextInput::make('nro_factura')
                    //                                 ->label('Nro. Factura/Nota de Entrega')
                    //                                 ->prefixIcon('heroicon-c-tag'),

                    //                             Forms\Components\TextInput::make('responsable')
                    //                                 ->prefixIcon('heroicon-s-home')
                    //                                 ->label('Cargado por:')
                    //                                 ->disabled()
                    //                                 ->dehydrated()
                    //                                 ->default(Auth::user()->name),

                    //                         ])


                    //                     ])->columns(2),
                    //             ]),
                    //     ])
                    //     ->action(function (Articulo $record, array $data) {
                    //         //entrada al inventario
                    //         $entrada = InventarioController::entrada($data, $record);

                    //         //carga del asiento para el movimiento del inventario
                    //     }),

                ])->dropdownPlacement('bottom-start')
                    ->size(ActionSize::Small)
                ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticulos::route('/'),
            'create' => Pages\CreateArticulo::route('/create'),
            'edit' => Pages\EditArticulo::route('/{record}/edit'),
        ];
    }
}
