<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // Menampilkan data customer & sopir ke form saat edit
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->customer) {
            $data['customer'] = [
                'nama_perusahaan' => $this->record->customer->nama_perusahaan,
                'alamat' => $this->record->customer->alamat,
            ];
        }

        if ($this->record->sopir) {
            $data['sopir'] = [
                'no_sim' => $this->record->sopir->no_sim,
                'telepon' => $this->record->sopir->telepon,
            ];
        }

        return $data;
    }

    // Menyimpan data customer & sopir saat update
    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $record = $this->record;

        // Penanganan data customer
        if ($data['role'] === 'customer') {
            $customerData = $data['customer'] ?? [];
            $record->customer()->updateOrCreate(
                ['user_id' => $record->id],
                [
                    'nama_perusahaan' => $customerData['nama_perusahaan'] ?? '',
                    'alamat' => $customerData['alamat'] ?? '',
                ]
            );
        } else {
            $record->customer()->delete();
        }

        // Penanganan data sopir (driver)
        if ($data['role'] === 'driver') {
            $sopirData = $data['sopir'] ?? [];
            $record->sopir()->updateOrCreate(
                ['user_id' => $record->id],
                [
                    'no_sim' => $sopirData['no_sim'] ?? '',
                    'telepon' => $sopirData['telepon'] ?? '',
                ]
            );
        } else {
            $record->sopir()->delete();
        }
    }

    // Redirect ke halaman daftar user setelah update
    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
