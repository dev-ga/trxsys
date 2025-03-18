<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Contrato;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Mantenimiento;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use App\Models\ValuacionCorrectivo;
use Filament\Forms\Components\FileUpload;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ValuacionCorrectivoResource\Pages;
use App\Filament\Resources\ValuacionCorrectivoResource\RelationManagers;

class ValuacionCorrectivoResource extends Resource
{
    protected static ?string $model = ValuacionCorrectivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('INFORMACION PRINCIPAL PARA LA CARGA DE LAS VALUACIONES')
                    ->description('Formulario para la carga de las valuaciones. Campos Requeridos(*)')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->schema([

                        Forms\Components\TextInput::make('codigo')
                            ->label('Codigo')
                            ->prefixIcon('heroicon-c-tag')
                            ->default(function () {
                                if (ValuacionCorrectivo::max('id') == null) {
                                    $parte_entera = 0;
                                } else {
                                    $parte_entera = ValuacionCorrectivo::max('id');
                                }
                                return '000' . $parte_entera + 1;
                            }),

                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Ejemplo: valuacion 1')
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

                        Forms\Components\Select::make('nro_contrato')
                            ->label('Nro. Contrato')
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->options(function (Get $get) {
                                return Contrato::where('empresa_contratante_id', $get('empresa_contratante_id'))->pluck('nro_contrato', 'nro_contrato');
                            })
                            ->required()
                            ->searchable()
                            ->live(),

                        Forms\Components\TextInput::make('monto_usd')
                            ->label('Monto USD')
                            ->placeholder('Ejemplo: 12654.90')
                            ->hint('separador decimal (.)')
                            ->numeric()
                            ->prefixIcon('heroicon-s-pencil')
                            ->required(),

                        FileUpload::make('doc_pdf')
                            ->label('Valuacion(.pdf)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),


                        Forms\Components\TextInput::make('responsable')
                            ->prefixIcon('heroicon-c-user-circle')
                            ->label('Cargado por:')
                            ->disabled()
                            ->dehydrated()
                            ->default(auth()->user()->name),


                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('empresa_contratante_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doc_pdf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mantenimiento_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
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
            'index' => Pages\ListValuacionCorrectivos::route('/'),
            'create' => Pages\CreateValuacionCorrectivo::route('/create'),
            'edit' => Pages\EditValuacionCorrectivo::route('/{record}/edit'),
        ];
    }
}