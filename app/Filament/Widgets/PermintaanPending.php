<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Permintaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class PermintaanPending extends BaseWidget
{
    protected null|string $heading = 'Statistik Permintaan';

    protected function getStats(): array
    {
        $permintaans = Permintaan::count();

        $jumlahPermintaanBulanIni = Permintaan::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        return [
            Stat::make('Total Bulan ini', $jumlahPermintaanBulanIni),
            Stat::make('Permintaan Menunggu', Permintaan::where('status_verifikasi', 'menunggu')->count())
                ->description('Belum diverifikasi'),

            Stat::make('Permintaan Disetujui', Permintaan::where('status_verifikasi', 'disetujui')->count())
                ->description('Sudah diverifikasi'),
            Stat::make('Permintaan Dijadwalkan', Permintaan::where('status_verifikasi', 'dijadwalkan')->count())
                ->description('Sudah dijadwalkan'),

            Stat::make('Permintaan Dalam Proses', Permintaan::where('status_verifikasi', 'Dalam Proses')->count())
                ->description('Sedang dikirim'),

            Stat::make('Permintaan Selesai', Permintaan::where('status_verifikasi', 'selesai')->count())
                ->description('Sudah selesai'),
        ];
    }
    public static function canView(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_direksi', 'operasional_pengiriman']);
    
    }
}
