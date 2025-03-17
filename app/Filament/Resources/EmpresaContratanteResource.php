<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmpresaContratanteResource\Pages;
use App\Filament\Resources\EmpresaContratanteResource\RelationManagers;

class EmpresaContratanteResource extends Resource
{
    protected static ?string $model = EmpresaContratante::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Empresa Contratante')
                ->description('Formulario para la carga de empresas contratantes. Campos Requeridos(*)')
                ->icon('heroicon-o-building-library')
                ->schema([

                    Forms\Components\TextInput::make('codigo')
                        ->label('Código')
                        ->prefixIcon('heroicon-c-tag')
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
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Nombre/Razon Social')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('ci_rif')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('CI/RIF')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('responsable')
                        ->prefixIcon('heroicon-s-home')
                        ->label('Cargado por:')
                        ->disabled()
                        ->dehydrated()
                        ->default(Auth::user()->name),

                ])->columns(4)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->badge()
                    ->color('marronClaro')
                    ->label('Nombre/Razon Social')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci_rif')
                    ->label('CI/RIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->label('Cargado por:')
                    ->icon('heroicon-c-user-circle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresaContratantes::route('/'),
            'create' => Pages\CreateEmpresaContratante::route('/create'),
            'edit' => Pages\EditEmpresaContratante::route('/{record}/edit'),
        ];
    }

    public static function updateTotales(Get $get, Set $set): void
    {

        if ($get('monto_mante_prev_usd') != 0 && $get('monto_mante_correc_usd') != 0) {

        }

        if ($get('feedback') == false && $get('forma_pago') == 'dolares') {
            $set('conversion_a_usd', round($get('monto_usd'), 2));
        }

        if ($get('feedback') == false && $get('forma_pago') == 'bolivares') {
            $set('total_gasto_bsd',  round($get('monto_bsd'), 2));
            $set('conversion_a_usd', round($get('monto_bsd') / $get('tasa_bcv'), 2));
        }
    }
}
