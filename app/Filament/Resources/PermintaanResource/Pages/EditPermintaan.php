<?php

namespace App\Filament\Resources\PermintaanResource\Pages;

use App\Filament\Resources\PermintaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaan extends EditRecord
{
    protected static string $resource = PermintaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn($record) => auth()->user()->role === 'admin'),
        ];
    }

    public function mount($record): void
    {
        parent::mount($record);

        if (
            in_array($this->record->status_verifikasi, ['disetujui', 'ditolak']) &&
            auth()->user()->role !== 'admin'
        ) {
            abort(403, 'Data ini sudah disetujui atau ditolak dan tidak bisa diubah.');
        }
    }

    protected function getRedirectUrl(): string
    {
        return PermintaanResource::getUrl('index');
    }
}
