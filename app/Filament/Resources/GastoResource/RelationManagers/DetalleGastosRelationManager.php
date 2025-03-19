<?php

namespace App\Filament\Resources\GastoResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gasto;
use App\Models\Agencia;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DetalleGastosRelationManager extends RelationManager
{
    protected static string $relationship = 'detalleGastos';

    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //         Section::make('Detalle de Gasto')
    //             ->description('Debe llenar los campos de forma correcta. Campos Requeridos(*)')
    //             ->icon('heroicon-c-users')
    //             ->schema([
    //                 Grid::make()
    //                     ->schema([

    //                         TextInput::make('nro_factura')
    //                             ->label('Nro. Factura')
    //                             ->prefixIcon('heroicon-c-users')
    //                             ->readOnly()
    //                             ->default(function (RelationManager $livewire) {
    //                                 return $livewire->ownerRecord->nro_factura;
    //                             })
    //                             ->disabled()
    //                             ->dehydrated(),
    //                         //codigo de requisicion
    //                         TextInput::make('empresa_contratante_id')
    //                             ->label('Empresa Contratante')
    //                             ->prefixIcon('heroicon-c-users')
    //                             ->readOnly()
    //                             ->default(function (RelationManager $livewire) {
    //                                 return $livewire->ownerRecord->empresaContratante->nombre;
    //                             })
    //                             ->disabled()
    //                             ->dehydrated(),

    //                         TextInput::make('nro_contrato')
    //                             ->label('Nro. Contrato')
    //                             ->prefixIcon('heroicon-c-users')
    //                             ->readOnly()
    //                             ->default(function (RelationManager $livewire) {
    //                                 return $livewire->ownerRecord->contrato->nro_contrato;
    //                             })
    //                             ->disabled()
    //                             ->dehydrated(),
    //                     ])->columns(3),
    //             ]),

    //         Section::make('Productos para requisicion')
    //             ->icon('heroicon-c-users')
    //             ->schema([
    //                 Grid::make()
    //                     ->schema([
    //                         Repeater::make('agencias')
    //                             ->schema([
    //                                 Grid::make()
    //                                     ->schema([
    //                                         //Tipo de gasto = contrato
    //                                         //--------------------------------------------------------
    //                                         Select::make('agencia_id')
    //                                             ->label('Agencia')
    //                                             ->prefixIcon('heroicon-m-list-bullet')
    //                                             ->options(function (RelationManager $livewire) {
    //                                                 return Agencia::where('contrato_id', $livewire->ownerRecord->contrato_id)->pluck('nombre', 'id');
    //                                             })
    //                                             ->searchable()
    //                                             ->live(),

    //                                         TextInput::make('monto_usd')
    //                                             ->label('Monto en Dolares($)')
    //                                             ->prefixIcon('heroicon-c-credit-card')
    //                                             ->numeric()
    //                                             ->hidden(function (RelationManager $livewire) {
    //                                                 if ($livewire->ownerRecord->monto_usd == 0) {
    //                                                     return true;
    //                                                 }

    //                                                 if ($livewire->ownerRecord->monto_usd != 0) {
    //                                                     return false;
    //                                                 }
    //                                             })
    //                                             ->placeholder('0.00'),

    //                                         TextInput::make('monto_bsd')
    //                                             ->label('Monto en Bolivares(Bs.)')
    //                                             ->prefixIcon('heroicon-c-credit-card')
    //                                             ->numeric()
    //                                             ->hidden(function (RelationManager $livewire) {
    //                                                 if ($livewire->ownerRecord->monto_bsd == 0) {
    //                                                     return true;
    //                                                 }

    //                                                 if ($livewire->ownerRecord->monto_bsd != 0) {
    //                                                     return false;
    //                                                 }
    //                                             })
    //                                             ->placeholder('0.00'),
    //                                         //--------------------------------------------------------
    //                                     ])->columns(2)
    //                             ])->columnSpanFull(),
    //                     ])->columns(3),
    //             ])->collapsible(),
    //         ]);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('gasto_id')
            ->description('Detalle de Gasto asociado a la factura')
            ->columns([
                Tables\Columns\TextColumn::make('gasto_id')
                    ->label('ID')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo_gasto')
                    ->label('Codigo')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->badge()
                    ->color('marronClaro'),
                Tables\Columns\TextColumn::make('valuacion_id')
                    ->label('Valuacion')
                    ->badge()
                    ->color('marronClaro'),
                Tables\Columns\TextColumn::make('agencia.nombre'),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->label('Monto(Bs.)')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->label('Tasa BCV')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->label('Monto(USD)')
                    ->summarize(Sum::make()
                        ->label('Total(USD)'))

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

    //beforeSave
    public function beforeSave(Gasto $record, array $data): array
    {
        dd($data);
        $data['monto_usd'] = $data['monto_bsd'] / $data['tasa_bcv'];
        return $data;
    }
}