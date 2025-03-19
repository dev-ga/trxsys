<?php

namespace App\Filament\Resources\GastoResource\Pages;

use App\Filament\Resources\GastoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGasto extends CreateRecord
{
    protected static string $resource = GastoResource::class;

    //Redireccion
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

//redirect