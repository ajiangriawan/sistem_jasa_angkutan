<?php

namespace App\Filament\Resources\KontrakKerjaResource\Pages;

use App\Filament\Resources\KontrakKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKontrakKerjas extends ListRecords
{
    protected static string $resource = KontrakKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
