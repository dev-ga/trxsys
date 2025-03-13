<?php

namespace App\Filament\Resources\MantenimientoCorrectivoResource\Pages;

use App\Filament\Resources\MantenimientoCorrectivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMantenimientoCorrectivos extends ListRecords
{
    protected static string $resource = MantenimientoCorrectivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}