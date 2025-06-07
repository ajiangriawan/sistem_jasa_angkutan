<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
/*
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
        */
}
