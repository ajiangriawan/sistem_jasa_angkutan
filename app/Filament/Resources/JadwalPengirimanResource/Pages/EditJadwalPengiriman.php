<?php

namespace App\Filament\Resources\JadwalPengirimanResource\Pages;

use App\Filament\Resources\JadwalPengirimanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalPengiriman extends EditRecord
{
    protected static string $resource = JadwalPengirimanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
