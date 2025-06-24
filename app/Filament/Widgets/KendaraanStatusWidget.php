<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Auth;


class KendaraanStatusWidget extends BaseWidget
{
    protected function getHeading(): string
    {
        return 'Ringkasan Informasi Kendaraan';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Siap', Kendaraan::where('status', 'siap')->count())->color('success'),
            Stat::make('Dijadwalkan', Kendaraan::where('status', 'dijadwalkan')->count())->color('warning'),
            Stat::make('Beroperasi', Kendaraan::where('status', 'beroperasi')->count())->color('info'),
            Stat::make('Perbaikan', Kendaraan::where('status', 'perbaikan')->count())->color('danger'),
            Stat::make('Rusak', Kendaraan::where('status', 'rusak')->count())->color('gray'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_direksi', 'operasional_transportasi']);
    
    }
}
