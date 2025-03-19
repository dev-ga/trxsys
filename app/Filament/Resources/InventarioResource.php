<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Almacen;
use Filament\Forms\Form;
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
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Http\Controllers\InventarioController;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InventarioResource\Pages;
use App\Filament\Resources\InventarioResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $activeNavigationIcon = 'heroicon-s-queue-list';

    protected static ?string $recordTitleAttribute = 'articulo.descripcion';

    protected static ?string $navigationGroup = 'Manejo de Inventario';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->articulo->descripcion;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                ->label('Código')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('articulo_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('almacen_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('responsable')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('articulo.descripcion')
                    ->label('Descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('almacen.descripcion')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
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

                Action::make('reposicion')
                    ->label('Reposicion')
                    ->color('negro')
                    ->icon('heroicon-m-arrows-right-left')
                    ->model(Inventario::class)
                    ->form([
                        Section::make('Entrada de Inventario')
                        ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                        ->icon('heroicon-m-arrows-right-left')
                        ->schema([
                            Grid::make()
                            ->schema([

                                Forms\Components\TextInput::make('nro_factura')
                                    ->label('Nro. Factura/Nota de Entrega')
                                    ->prefixIcon('heroicon-c-tag')
                                    ->helperText('Este dato sera utilizado al momento de realizar auditorias de gastos contra los movimientos del inventario.'),
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantida entrante')
                                    ->prefixIcon('heroicon-c-tag')
                                    ->required(),
                                Forms\Components\TextInput::make('responsable')
                                        ->prefixIcon('heroicon-s-home')
                                        ->label('Cargado por:')
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(Auth::user()->name),

                            ])->columns(3),
                        ]),
                    ])
                    ->action(function (Inventario $record, array $data) {
                        //entrada al inventario
                        $entrada = InventarioController::reposicion($data, $record);

                        //carga del asiento para el movimiento del inventario
                    }),

                Action::make('salida')
                    ->label('Salida')
                    ->color('negro')
                    ->icon('heroicon-c-inbox-arrow-down')
                    ->model(Inventario::class)
                    ->form([
                        Section::make('Entrada de Inventario')
                            ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                            ->icon('heroicon-c-inbox-arrow-down')
                            ->schema([
                                Grid::make()
                                    ->schema([

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
                    ->action(function (Inventario $record, array $data) {
                        //entrada al inventario
                        $entrada = InventarioController::entrada($data, $record);

                        //carga del asiento para el movimiento del inventario
                    }),

            ])->dropdownPlacement('bottom-start')
                ->size(ActionSize::Small)
        ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Action::make('reposicion')
                        ->label('Reposicion')
                        ->color('negro')
                        ->icon('heroicon-m-arrows-right-left')
                        ->model(Inventario::class)
                        ->form([
                            Section::make('Entrada de Inventario')
                                ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
                                ->icon('heroicon-m-arrows-right-left')
                                ->schema([
                                    Grid::make()
                                        ->schema([

                                            Forms\Components\TextInput::make('nro_factura')
                                                ->label('Nro. Factura/Nota de Entrega')
                                                ->prefixIcon('heroicon-c-tag')
                                                ->helperText('Es deto sera utilizado al momento de realizar auditorias de gastos contra los movimientos del inventario.'),
                                            Forms\Components\TextInput::make('cantidad')
                                                ->label('Cantida entrante')
                                                ->prefixIcon('heroicon-c-tag')
                                                ->required(),
                                            Forms\Components\TextInput::make('responsable')
                                                ->prefixIcon('heroicon-s-home')
                                                ->label('Cargado por:')
                                                ->disabled()
                                                ->dehydrated()
                                                ->default(Auth::user()->name),

                                        ])->columns(3),
                                ]),
                        ])
                        ->action(function (Inventario $record, array $data) {
                            //entrada al inventario
                            $entrada = InventarioController::reposicion($data, $record);

                            //carga del asiento para el movimiento del inventario
                        }),

            ]),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['codigo', 'articulo.descripcion', 'almacen.descripcion', 'nro_factura', 'responsable'];
    }

    // public static function getGlobalSearchEloquentQuery(): Builder
    // {
    //     return parent::getGlobalSearchEloquentQuery()->with(['articulo']);
    // }

    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     return [
    //         'Artículo' => $record->articulo->descripcion,
    //     ];
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'edit' => Pages\EditInventario::route('/{record}/edit'),
        ];
    }
}
