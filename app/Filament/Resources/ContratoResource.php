<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Contrato;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ContratoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ContratoResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;

class ContratoResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-currency-dollar';

    protected static ?string $recordTitleAttribute = 'nro_contrato';

    protected static ?string $navigationGroup = 'Gestion de Proyectos';

    public static function getGlobalSearchResultTitle (Model $record): string
    {
        return $record->denominacion;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Contrato')
                ->description('Formulario para la carga de contratos. Campos Requeridos(*)')
                ->icon('heroicon-o-document-currency-dollar')
                ->schema([
                    Forms\Components\Select::make('empresa_contratante_id')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->relationship('empresaContratante', 'nombre')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('codigo')
                                ->label('Código')
                                ->default(function () {
                                    if (EmpresaContratante::max('id') == null) {
                                        $parte_entera = 0;
                                    } else {
                                        $parte_entera = EmpresaContratante::max('id');
                                    }
                                    return '000' . $parte_entera + 1;
                                })
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('nombre')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('ci_rif')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('responsable')
                                ->prefixIcon('heroicon-s-home')
                                ->label('Cargado por:')
                                ->disabled()
                                ->dehydrated()
                                ->default(Auth::user()->name),
                        ]),

                        Forms\Components\TextInput::make('nro_contrato')
                            ->label('Nro. Contrato')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('denominacion')
                            ->label('Definición')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mant_prev_usd')
                            ->label('Monto Mantenimiento Preventivo(USD)')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('mant_correc_usd')
                            ->label('Monto Mantenimiento Correctivo(USD)')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('monto_total_usd')
                            ->label('Monto Total(USD)')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('responsable')
                            ->prefixIcon('heroicon-c-user-circle')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(Auth::user()->name),
                ])->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->label('Empresa Contratante')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('denominacion')
                    ->label('Definición')
                    ->badge()
                    ->color('azul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->label('Nro. Contrato')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mant_prev_usd')
                    ->label('Mant. Prev. USD')
                    ->money('USD')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mant_correc_usd')
                    ->label('Mant. Correc. USD')
                    ->money('USD')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_total_usd')
                    ->label('Monto Total USD')
                    ->money('USD')
                    ->searchable()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->label('Cargado por:')
                    ->icon('heroicon-c-user-circle')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->badge()
                    ->icon('heroicon-c-calendar')
                    ->color('naranja')
                    ->label('Fecha de Registro')
                    ->dateTime('d-m-Y')
                    ->sortable(),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ValuacionesRelationManager::class,
            RelationManagers\AgenciasRelationManager::class,
            RelationManagers\EquiposRelationManager::class,
            RelationManagers\MantenimientoPreventivosRelationManager::class,
            RelationManagers\MantenimientoCorrectivosRelationManager::class,
            RelationManagers\BitacorasRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
            return ['nro_contrato', 'mant_correc_usd', 'empresaContratante.nombre', 'monto_total_usd', 'mant_prev_usd', 'denominacion'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContratos::route('/'),
            'create' => Pages\CreateContrato::route('/create'),
            'edit' => Pages\EditContrato::route('/{record}/edit'),
        ];
    }
}
