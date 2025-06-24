<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends StatsOverviewWidget
{
    protected null|string $heading = 'Ringkasan Pengguna';
    protected function getStats(): array
    {
        $users = User::count();
        $activeUsers = User::where('status', 'aktif')->count();
        $inactiveUsers = User::where('status', 'tidak aktif')->count();

        $activeDrivers = User::where('role', 'operasional_sopir')->where('status', 'aktif')->count();

        $scheduledDrivers = User::where('role', 'operasional_sopir')
            ->whereHas('jadwalPengiriman', function ($q) {
                $q->whereDate('tanggal_berangkat', '>=', Carbon::today());
            })
            ->count();

        $onDutyDrivers = User::where('role', 'operasional_sopir')
            ->whereHas('jadwalPengiriman', function ($q) {
                $q->whereDate('tanggal_berangkat', Carbon::today());
            })
            ->count();

        return [
            Stat::make('Pengguna Aktif / Total', $activeUsers . '/'. $users),
            //Stat::make('Pengguna Tidak Aktif', $inactiveUsers),
            Stat::make('Sopir Aktif', $activeDrivers),
            Stat::make('Sopir Dijadwalkan', $scheduledDrivers),
            Stat::make('Sopir Bertugas Hari Ini', $onDutyDrivers),
        ];
    }
}
