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

    protected static ?string $navigationIcon = 'heroicon-m-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Proveedor')
                ->description('Formulario para la carga de proveedores. Campos Requeridos(*)')
                ->icon('heroicon-m-truck')
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
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('ci_rif')
                    ->prefixIcon('heroicon-s-pencil')
                        ->label('CI/RIF')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('nombre')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Nombre/Razón Social')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('direccion')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Dirección')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono_local')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Tel. Local')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono_cel')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Tel. Celular')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->prefixIcon('heroicon-s-pencil')
                        ->label('Correo')
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
                    ->label('Codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci_rif')
                    ->badge()
                    ->color('naranja')
                    ->label('CI/RIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->badge()
                    ->color('naranja')
                    ->label('Nombre/Razón Social')
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
                    ->label('Cargado por:')
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
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}