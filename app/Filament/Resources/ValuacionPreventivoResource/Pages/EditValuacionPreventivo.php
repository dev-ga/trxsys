<?php

namespace App\Filament\Resources\ValuacionPreventivoResource\Pages;

use App\Filament\Resources\ValuacionPreventivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditValuacionPreventivo extends EditRecord
{
    protected static string $resource = ValuacionPreventivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
