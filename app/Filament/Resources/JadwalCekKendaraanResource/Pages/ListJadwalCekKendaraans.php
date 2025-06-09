<?php

namespace App\Filament\Resources\JadwalCekKendaraanResource\Pages;

use App\Filament\Resources\JadwalCekKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListJadwalCekKendaraans extends ListRecords
{
    protected static string $resource = JadwalCekKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with([
                'permintaan.laporan.sopir',
                'teknisi',
            ]);
    }
}
