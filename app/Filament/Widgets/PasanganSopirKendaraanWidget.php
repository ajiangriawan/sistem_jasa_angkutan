<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\PasanganSopirKendaraan;
use Filament\Tables\Columns\TextColumn;

class PasanganSopirKendaraanWidget extends BaseWidget
{
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return PasanganSopirKendaraan::with(['sopir', 'kendaraan']);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('sopir.name')->label('Sopir'),
            TextColumn::make('kendaraan.no_polisi')->label('No Polisi'),
            TextColumn::make('kendaraan.status')->label('Status')->badge(),
        ];
    }
    /*
    public static function canView(): bool
    {
        return auth()->user()?->role === 'operasional_transportasi';
    }
        */
}
