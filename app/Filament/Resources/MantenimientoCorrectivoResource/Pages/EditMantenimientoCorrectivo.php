<?php

namespace App\Filament\Resources\MantenimientoCorrectivoResource\Pages;

use App\Filament\Resources\MantenimientoCorrectivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMantenimientoCorrectivo extends EditRecord
{
    protected static string $resource = MantenimientoCorrectivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
