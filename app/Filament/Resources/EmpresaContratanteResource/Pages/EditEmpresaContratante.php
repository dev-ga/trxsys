<?php

namespace App\Filament\Resources\EmpresaContratanteResource\Pages;

use App\Filament\Resources\EmpresaContratanteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpresaContratante extends EditRecord
{
    protected static string $resource = EmpresaContratanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
