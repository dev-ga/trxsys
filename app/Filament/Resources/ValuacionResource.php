<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Contrato;
use Filament\Forms\Form;
use App\Models\Valuacion;
use Filament\Tables\Table;
use App\Models\Mantenimiento;
use Filament\Resources\Resource;
use App\Models\EmpresaContratante;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ValuacionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ValuacionResource\RelationManagers;

class ValuacionResource extends Resource
{
    protected static ?string $model = Valuacion::class;

    protected static ?string $navigationLabel = 'Valuaciones';

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';


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
                            if (Valuacion::max('id') == null) {
                                $parte_entera = 0;
                            } else {
                                $parte_entera = Valuacion::max('id');
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
                        ->required()
                        ->maxLength(255),

                        Forms\Components\Select::make('mantenimiento_id')
                        ->label('Tipo de Mantenimiento')
                        ->prefixIcon('heroicon-m-list-bullet')
                        ->options(Mantenimiento::all()->pluck('descripcion', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),

                        FileUpload::make('doc_pdf')
                        ->label('Valuacion(.pdf)')
                        ->acceptedFileTypes(['application/pdf'])
                        ->required(),


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
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('empresaContratante.nombre')
                    ->label('Empresa')
                    ->badge()
                    ->color('marronClaro')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_contrato')
                    ->label('Nro Contrato')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doc_pdf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monto_usd')
                    ->badge()
                    ->color('success')
                    ->money('USD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_bsd')
                    ->badge()
                    ->color('success')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mantenimiento.descripcion')
                    ->badge()
                    ->color('naranja')
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->searchable()
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
                Tables\Actions\EditAction::make()
                ->color('naranja'),
                Tables\Actions\Action::make('ver_pdf')
                ->label('Ver PDF')
                ->icon('heroicon-s-eye')
                ->color('naranja')
                ->url(function ($record) {
                    return asset('storage/' . $record->doc_pdf);
                }),
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
            'index' => Pages\ListValuacions::route('/'),
            'create' => Pages\CreateValuacion::route('/create'),
            'edit' => Pages\EditValuacion::route('/{record}/edit'),
        ];
    }
}
