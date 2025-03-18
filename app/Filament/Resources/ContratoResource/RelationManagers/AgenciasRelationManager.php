<?php

namespace App\Filament\Resources\ContratoResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Estado;
use Filament\Forms\Get;
use App\Models\Contrato;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\EmpresaContratante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AgenciasRelationManager extends RelationManager
{
    protected static string $relationship = 'agencias';



    protected static ?string $label = 'Agencias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('AGENCIAS')
                    ->description('Formulario para la carga de agencias.')
                    ->icon('heroicon-o-home')
                    ->schema([

                        Forms\Components\TextInput::make('codigo')
                            ->label('Código de agencia')
                            ->prefixIcon('heroicon-c-tag')
                            ->required(),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('empresa_contratante_id')
                            ->label('Empresa Contratante')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(EmpresaContratante::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->required()
                            ->live(),

                        // Forms\Components\Select::make('contrato_id')
                        //     ->label('Contrato asociado')
                        //     ->prefixIcon('heroicon-m-list-bullet')
                        //     ->options(function (Get $get) {
                        //         return Contrato::where('empresa_contratante_id', $get('empresa_contratante_id'))->pluck('nro_contrato', 'id');
                        //     })
                        //     ->searchable()
                        //     ->required()
                        //     ->live(),

                        Forms\Components\Select::make('estado_id')
                            ->label('Estado')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(Estado::all()->pluck('descripcion', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->prefixIcon('heroicon-s-pencil')
                            ->required()
                            ->maxLength(255),

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
            ->recordTitleAttribute('contrato_id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código de agencia')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre/Razón Social')
                    ->icon('heroicon-s-home')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->badge()
                    ->color('azul')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->dateTime('d-m-Y')
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
                ]),
            ]);
    }
}