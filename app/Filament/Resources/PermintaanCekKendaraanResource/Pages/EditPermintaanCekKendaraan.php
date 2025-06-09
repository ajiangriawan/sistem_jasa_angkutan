<?php

namespace App\Filament\Resources\PermintaanCekKendaraanResource\Pages;

use App\Filament\Resources\PermintaanCekKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanCekKendaraan extends EditRecord
{
    protected static string $resource = PermintaanCekKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->role === 'supervisor_bengkel' && $data['status'] !== 'menunggu') {
            $data['disetujui_oleh'] = auth()->id();
        }
        return $data;
    }
}
