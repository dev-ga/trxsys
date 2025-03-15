<?php

namespace App\Filament\Resources\EmpresaContratanteResource\Pages;

use App\Filament\Resources\EmpresaContratanteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmpresaContratantes extends ListRecords
{
    protected static string $resource = EmpresaContratanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
