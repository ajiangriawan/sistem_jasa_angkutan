<?php

namespace App\Filament\Resources\LaporanKendalaResource\Pages;

use App\Filament\Resources\LaporanKendalaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanKendala extends EditRecord
{
    protected static string $resource = LaporanKendalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
