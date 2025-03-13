<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Configuracion;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ConfiguracionResource\Pages;
use App\Filament\Resources\ConfiguracionResource\RelationManagers;

class ConfiguracionResource extends Resource
{
    protected static ?string $model = Configuracion::class;

    protected static ?string $navigationIcon = 'heroicon-c-cog-8-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Section::make('Configuraciones')
                ->description('Canfiguracion y valores del sistema. Campos Requeridos(*)')
                ->icon('heroicon-c-cog-8-tooth')
                ->schema([
                    Forms\Components\TextInput::make('iva')
                    ->label('IVA')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('iva_nomina')
                    ->label('IVA Nomina')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('isrl')
                    ->label('ISR')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('tasa_bcv')
                    ->label('Tasa(BCV)')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('anio_curso')
                    ->label('Año Curso')
                        ->numeric()
                        ->default(0.00),
                    
                ])->columns(5)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('iva')
                    ->label('IVA')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva_nomina')
                    ->label('IVA Nomina')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isrl')
                    ->label('ISR')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasa_bcv')
                    ->label('Tasa(BCV)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('anio_curso')
                    ->label('Año Curso')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListConfiguracions::route('/'),
            'create' => Pages\CreateConfiguracion::route('/create'),
            'edit' => Pages\EditConfiguracion::route('/{record}/edit'),
        ];
    }
}