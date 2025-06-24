<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class KeuanganChart extends LineChartWidget
{
    
    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $labels = [];
        $uangJalanData = [];
        $bonusData = [];
        $totalData = [];

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $labels[] = $date->format('d M');

            $invoices = Invoice::whereDate('created_at', $date)->get();
            $uangJalan = $invoices->sum('total_uang_jalan');

            // Jika ada kolom `bonus`, gunakan ini; jika tidak, tetapkan 0 atau hitung manual
            

            $uangJalanData[] = $uangJalan;
            /*
            $bonusData[] = $bonus;
            $totalData[] = $uangJalan + $bonus;
            */
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Uang Jalan',
                    'borderColor' => '#3b82f6',
                    'data' => $uangJalanData,
                    'fill' => false,
                ],
                /*
                [
                    'label' => 'Bonus',
                    'borderColor' => '#facc15',
                    'data' => $bonusData,
                    'fill' => false,
                ],
                [
                    'label' => 'Total Invoice',
                    'borderColor' => '#10b981',
                    'data' => $totalData,
                    'fill' => false,
                ],
                */
            ],
        ];
    }
}
