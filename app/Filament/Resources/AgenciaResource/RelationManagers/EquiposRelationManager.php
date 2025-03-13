<?php

namespace App\Filament\Resources\AgenciaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Agencia;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
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
                    ->dateTime()
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function afterSave(): void
    {
        dd($this->getOwnerRecord());
        $this->record->equipos()->syncWithoutDetaching([$this->options['categoryId']]);
    }
}