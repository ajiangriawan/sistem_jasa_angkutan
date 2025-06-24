<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\JadwalCekKendaraan;
use Illuminate\Support\Carbon;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class JadwalCekKendaraanWidget extends BaseWidget
{


    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return JadwalCekKendaraan::with('teknisi', 'permintaan.kendaraan')
            ->whereBetween('jadwal', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('jadwal')->label('Tanggal')->dateTime('d M Y H:i'),
            TextColumn::make('permintaan.kendaraan.no_polisi')->label('No Polisi'),
            TextColumn::make('teknisi.name')->label('Teknisi'),
            TextColumn::make('status')->badge(),
        ];
    }
    public static function canView(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_direksi', 'operasional_pengiriman', 'operasioan_teknisi']);
    }
}
