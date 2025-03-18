<?php

namespace App\Filament\Resources\ValuacionCorrectivoResource\Pages;

use App\Filament\Resources\ValuacionCorrectivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditValuacionCorrectivo extends EditRecord
{
    protected static string $resource = ValuacionCorrectivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
