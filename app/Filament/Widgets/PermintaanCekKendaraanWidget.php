<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\PermintaanCekKendaraan;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class PermintaanCekKendaraanWidget extends BaseWidget
{


    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return PermintaanCekKendaraan::with('kendaraan', 'laporan')
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('kendaraan.no_polisi')->label('Kendaraan'),
            TextColumn::make('laporan.deskripsi')->label('Kendala')->limit(30),
            TextColumn::make('status')->badge(),
            TextColumn::make('created_at')->dateTime('d M Y H:i'),
        ];
    }
    public static function canView(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_direksi', 'operasional_transportasi']);
    
    }
}
