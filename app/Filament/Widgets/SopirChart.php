<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use App\Models\DetailJadwalPengiriman;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SopirChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Sopir';

    protected function getData(): array
    {
        $today = Carbon::today();

        // 1. Jumlah sopir aktif
        $aktif = User::where('role', 'operasional_sopir')
            ->where('status', 'aktif')
            ->count();

        // 2. Jumlah sopir dijadwalkan hari ini (ada di detail_jadwal_pengirimans yang jadwalnya tanggal_berangkat >= hari ini)
        $dijadwalkan = DetailJadwalPengiriman::whereHas('jadwal', function ($query) use ($today) {
            $query->whereDate('tanggal_berangkat', '>=', $today);
        })->whereHas('pasangan.sopir')
            ->count();

        // 3. Jumlah sopir yang bertugas hari ini (jadwal dengan tanggal_berangkat = hari ini)
        $bertugas = DetailJadwalPengiriman::whereHas('jadwal', function ($query) use ($today) {
            $query->whereDate('tanggal_berangkat', $today);
        })->whereHas('pasangan.sopir')
            ->count();

        return [
            'labels' => ['Aktif', 'Dijadwalkan', 'Bertugas Hari Ini'],
            'datasets' => [
                [
                    'label' => 'Jumlah Sopir',
                    'data' => [$aktif, $dijadwalkan, $bertugas],
                    'backgroundColor' => ['#10B981', '#3B82F6', '#F59E0B'], // opsional warna
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // bisa diganti dengan 'line' atau 'pie' sesuai kebutuhan
    }
    public static function canView(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_hr', 'admin_direksi', 'operasional_transportasi']);
    
    }
}
