<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MantenimientoCorrectivosRelationManager extends RelationManager
{
    protected static string $relationship = 'MantenimientoCorrectivos';

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
                        ->relationship('agencia', 'nombre')
                        ->preload()
                        ->default(function (RelationManager $livewire) {
                            return $livewire->ownerRecord->agencia_id;
                        })
                        ->disabled()
                        ->dehydrated(),


                    Forms\Components\TextInput::make('codigo_equipo')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Codigo')
                        ->default(function (RelationManager $livewire) {
                            return $livewire->ownerRecord->codigo;
                        })
                        ->disabled()
                        ->dehydrated(),

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
            ->recordTitleAttribute('agencia_id')
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
                Tables\Columns\TextColumn::make('codigo_equipo')
                    ->badge()
                    ->color('marronClaro'),
                Tables\Columns\TextColumn::make('nro_presupuesto')
                    ->label('Nro. Presupuesto')
                    ->badge()
                    ->color('marronClaro')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_presupuesto_usd')
                    ->badge()
                    ->money('USD')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('detalles'),
                Tables\Columns\TextColumn::make('doc_pdf'),
                Tables\Columns\TextColumn::make('responsable')
                ->icon('heroicon-c-user-circle')
                ->label('Cargado por:'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    
}