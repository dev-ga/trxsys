<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Proveedor;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProveedorResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProveedorResource\RelationManagers;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationLabel = 'Proveedores';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Proveedor')
                ->description('Formulario para la carga de proveedores. Campos Requeridos(*)')
                ->icon('heroicon-o-truck')
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->default(function () {
                            if (Proveedor::max('id') == null) {
                                $parte_entera = 0;
                            } else {
                                $parte_entera = Proveedor::max('id');
                            }
                            return '000' . $parte_entera + 1;
                        })
                        ->label('Código')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('ci_rif')
                    ->prefixIcon('heroicon-s-pencil')
                        ->label('CI/RIF')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre/Razón Social')
                        ->prefixIcon('heroicon-s-pencil')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('direccion')
                        ->label('Dirección')
                        ->prefixIcon('heroicon-s-pencil')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono_local')
                        ->label('Tel. Local')
                        ->prefixIcon('heroicon-s-pencil')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono_cel')
                        ->label('Tel. Celular')
                        ->prefixIcon('heroicon-s-pencil')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Correo')
                        ->prefixIcon('heroicon-s-pencil')
                        ->email()
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci_rif')
                    ->label('CI/RIF')
                    ->badge()
                    ->color('azul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre/Razón Social')
                    ->badge()
                    ->color('naranja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telefono_local')
                    ->label('Tel. Local')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telefono_cel')
                    ->label('Tel. Celular')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('responsable')
                    ->icon('heroicon-c-user-circle')
                    ->label('Cargado por:')
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'ci_rif', 'codigo'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
