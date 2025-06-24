<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;

class UserChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Pengguna';

    protected function getData(): array
    {
        // Ambil data jumlah user per role
        $data = User::select('role')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('role')
            ->get()
            ->pluck('total', 'role')
            ->toArray();

        return [
            'labels' => array_keys($data),
            'datasets' => [
                [
                    'label' => 'Jumlah Pengguna',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#6366F1', // Indigo
                        '#10B981', // Emerald
                        '#F59E0B', // Amber
                        '#EF4444', // Red
                        '#3B82F6', // Blue
                        '#8B5CF6', // Violet
                        '#EC4899', // Pink
                        '#06B6D4', // Cyan (replaced similar blue)
                        '#84CC16', // Lime
                        '#F97316', // Orange (replaced similar red)
                        '#A855F7', // Purple (additional)
                        '#64748B', // Slate (additional)
                    ],

                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // atau 'doughnut' jika ingin tampilan tengah kosong
    }
}
