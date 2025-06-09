<?php

namespace App\Filament\Resources\JadwalCekKendaraanResource\Pages;

use App\Filament\Resources\JadwalCekKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalCekKendaraan extends EditRecord
{
    protected static string $resource = JadwalCekKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
