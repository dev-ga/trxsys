<?php

namespace App\Filament\Resources\ValuacionResource\Pages;

use App\Filament\Resources\ValuacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditValuacion extends EditRecord
{
    protected static string $resource = ValuacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
