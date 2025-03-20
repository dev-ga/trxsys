<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Equipo;
use App\Models\Agencia;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\EquipoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EquipoResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;

class EquipoResource extends Resource
{
    protected static ?string $model = Equipo::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $activeNavigationIcon = 'heroicon-s-server-stack';

    protected static ?string $recordTitleAttribute = 'codigo';

    protected static ?string $navigationGroup = 'Gestion de Proyectos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
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
                            ->preload()
                            ->searchable()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $codigo_agencia = Agencia::where('id', $get('agencia_id'))->first()->codigo;
                                $codigo = 'TRX-' . $codigo_agencia . '-' . rand(11111, 99999);
                                $set('codigo', $codigo);
                            })
                            ->required(),
                        Forms\Components\TextInput::make('codigo')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Código de equipo')
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
                    ])->columns(4),

                    Section::make('CONDENSADORA')
                    ->description('Caracteristicas de la condensadora. Campos Requeridos(*)')
                    ->schema([
                        Forms\Components\TextInput::make('toneladas')
                            ->required()
                            ->numeric()
                            ->live()
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Toneladas'),

                        Forms\Components\Select::make('PH')
                            ->required()
                            ->label('PH(Phase)')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                            ])
                            ->searchable(),

                        Forms\Components\Select::make('refrigerante')
                            ->required()
                            ->label('Refrigerante')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                'R-22'  => 'R-22',
                                'R-410' => 'R-410',
                            ])
                            ->searchable(),


                        Forms\Components\Select::make('voltaje')
                            ->required()
                            ->label('Voltaje')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options([
                                '110v'  => '110v',
                                '220v'  => '220v',
                                '440v'  => '440v',
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('motor_ventilador_hp')
                            ->required()
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Motor Ventilador(Hp)'),

                        Forms\Components\TextInput::make('motor_ventilador_eje')
                            ->required()
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Motor Ventilador(Eje)'),

                        Forms\Components\TextInput::make('tipo_correa')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('Tipo de correa'),
                        Forms\Components\TextInput::make('rpm')
                            ->prefixIcon('heroicon-s-pencil')
                            ->label('RPM'),
                    ])->columns(4),

                    Section::make('EVAPORADORA')
                        ->description('Caracteristicas de la evaporadora. Campos Requeridos(*)')
                        ->schema([
                            Forms\Components\TextInput::make('toneladas_eva')
                                ->required()
                                ->numeric()
                                ->live()
                                ->prefixIcon('heroicon-s-pencil')
                                ->label('Toneladas'),

                            Forms\Components\Select::make('ph_eva')
                                ->required()
                                ->label('PH(Phase)')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options([
                                    '1' => '1',
                                    '2' => '2',
                                    '3' => '3',
                                ])
                                ->searchable(),

                            Forms\Components\Select::make('refrigerante_eva')
                                ->required()
                                ->label('Refrigerante')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options([
                                    'R-22'  => 'R-22',
                                    'R-410' => 'R-410',
                                ])
                                ->searchable(),


                            Forms\Components\Select::make('voltaje_eva')
                                ->required()
                                ->label('Voltaje')
                                ->prefixIcon('heroicon-m-list-bullet')
                                ->options([
                                    '110v'  => '110v',
                                    '220v'  => '220v',
                                    '440v'  => '440v',
                                ])
                                ->searchable(),
                            Forms\Components\TextInput::make('motor_ventilador_hp_eva')
                                ->required()
                                ->prefixIcon('heroicon-s-pencil')
                                ->label('Motor Ventilador(Hp)'),

                            Forms\Components\TextInput::make('motor_ventilador_eje_eva')
                                ->required()
                                ->prefixIcon('heroicon-s-pencil')
                                ->label('Motor Ventilador(Eje)'),

                            Forms\Components\TextInput::make('tipo_correa_eva')
                                ->prefixIcon('heroicon-s-pencil')
                                ->label('Tipo de correa'),
                            Forms\Components\TextInput::make('rpm_eva')
                                ->prefixIcon('heroicon-s-pencil')
                                ->label('RPM'),
                        ])->columns(4),

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

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Equipo')
                    ->description(function (Equipo $record): string {
                        return 'Agencia: '.$record->agencia->nombre;
                    })
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('rpm')
                    ->label('RPM')
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
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('desde'),
                        DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['hasta'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators['desde'] = 'Venta desde ' . Carbon::parse($data['desde'])->toFormattedDateString();
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators['hasta'] = 'Venta hasta ' . Carbon::parse($data['hasta'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                SelectFilter::make('agencia')
                    ->relationship('agencia', 'nombre')
                    ->searchable()
                    ->preload()
                    ->attribute('agencia_id'),

            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    //Relacion 1 a N con la tabla de Bitacoras
    public static function getRelations(): array
    {
        return [
            RelationManagers\MantenimientoPreventivosRelationManager::class,
            RelationManagers\MantenimientoCorrectivosRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
            return ['codigo', 'agencia.nombre', 'toneladas', 'PH', 'refrigerante', 'area_suministro', 'voltaje'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['agencia']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Agencia' => $record->agencia->nombre,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipos::route('/'),
            'create' => Pages\CreateEquipo::route('/create'),
            'edit' => Pages\EditEquipo::route('/{record}/edit'),
        ];
    }
}