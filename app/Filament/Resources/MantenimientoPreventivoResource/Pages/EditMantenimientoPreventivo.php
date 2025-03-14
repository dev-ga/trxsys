<?php

namespace App\Filament\Resources\MantenimientoPreventivoResource\Pages;

use App\Filament\Resources\MantenimientoPreventivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMantenimientoPreventivo extends EditRecord
{
    protected static string $resource = MantenimientoPreventivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
