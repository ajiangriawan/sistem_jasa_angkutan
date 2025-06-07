<?php

namespace App\Filament\Resources\PasanganSopirKendaraanResource\Pages;

use App\Filament\Resources\PasanganSopirKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPasanganSopirKendaraan extends EditRecord
{
    protected static string $resource = PasanganSopirKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return PasanganSopirKendaraanResource::getUrl('index');
    }
}
