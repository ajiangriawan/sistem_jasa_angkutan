<?php

namespace App\Filament\Resources\PermintaanCekKendaraanResource\Pages;

use App\Filament\Resources\PermintaanCekKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPermintaanCekKendaraans extends ListRecords
{
    protected static string $resource = PermintaanCekKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn() => Auth::user()?->role === 'operasional_transportasi'),
        ];
    }
}
