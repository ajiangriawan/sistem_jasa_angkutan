<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Permintaan;
use Illuminate\Support\Facades\Auth;

class PermintaanPending extends BaseWidget
{
    protected null|string $heading = 'Statistik Permintaan';

    protected function getStats(): array
    {
        return [
            Stat::make('Permintaan Menunggu', Permintaan::where('status_verifikasi', 'menunggu')->count())
                ->description('Belum diverifikasi'),

            Stat::make('Permintaan Disetujui', Permintaan::where('status_verifikasi', 'disetujui')->count())
                ->description('Sudah diverifikasi'),

            Stat::make('Permintaan Dalam Proses', Permintaan::where('status_verifikasi', 'Dalam Proses')->count())
                ->description('Sedang dikirim'),

            Stat::make('Permintaan Selesai', Permintaan::where('status_verifikasi', 'selesai')->count())
                ->description('Sudah selesai'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'operasional_pengiriman';
    }
}
