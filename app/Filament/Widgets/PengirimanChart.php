<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\JadwalPengiriman;

class PengirimanChart extends ChartWidget
{
    protected static ?string $heading = 'Status Pengiriman';

    protected function getType(): string
    {
        return 'pie'; // Gunakan pie chart
    }

    protected function getData(): array
    {
        // Ambil jumlah status pengiriman
        $statusFilter = ['dijadwalkan', 'selesai', 'Dalam Proses'];

        $statuses = JadwalPengiriman::whereIn('status', $statusFilter)
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();


        return [
            'labels' => array_map('ucwords', array_keys($statuses)),
            'datasets' => [
                [
                    'label' => 'Status Pengiriman',
                    'data' => array_values($statuses),
                    'backgroundColor' => [
                        '#3b82f6', // biru - dijadwalkan
                        '#10b981', // hijau - selesai
                        '#f59e0b', // oranye - proses/dalam perjalanan
                        '#ef4444', // merah - dibatalkan (jika ada)
                    ],
                ],
            ],
        ];
    }
}
