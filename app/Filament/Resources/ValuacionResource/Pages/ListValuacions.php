<?php

namespace App\Filament\Resources\ValuacionResource\Pages;

use App\Filament\Resources\ValuacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListValuacions extends ListRecords
{
    protected static string $resource = ValuacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
