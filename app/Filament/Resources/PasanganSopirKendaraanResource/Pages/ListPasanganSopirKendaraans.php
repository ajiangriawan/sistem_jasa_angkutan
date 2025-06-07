<?php

namespace App\Filament\Resources\PasanganSopirKendaraanResource\Pages;

use App\Filament\Resources\PasanganSopirKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPasanganSopirKendaraans extends ListRecords
{
    protected static string $resource = PasanganSopirKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
