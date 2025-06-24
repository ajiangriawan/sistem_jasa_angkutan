<?php

namespace App\Filament\Widgets;

use App\Models\Deposit;
use App\Models\Invoice;
use App\Models\Permintaan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KeuanganOverview extends BaseWidget
{
    protected function getHeading(): string
    {
        return 'Ringkasan Keuangan Bulanan';
    }


    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Total invoice bulan ini
        $invoiceBulanIni = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

        $totalInvoice = $invoiceBulanIni->sum('total_uang_jalan');
        $jumlahInvoice = $invoiceBulanIni->count();

        // Deposit customer
        $depositMasuk = Deposit::where('status', 'diterima')
            ->where('jumlah', '>', 0) // Add this condition to filter positive values
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');
        $depositMenunggu = Deposit::where('status', 'menunggu')->count();

        // Statistik status invoice
        $statusInvoice = [
            'menunggu' => Invoice::where('status', 'menunggu_persetujuan')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'disetujui' => Invoice::where('status', 'disetujui')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'ditolak' => Invoice::where('status', 'ditolak')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
        ];

        return [
            Stat::make('Total Invoice Bulan Ini', 'Rp ' . number_format($totalInvoice, 0, ',', '.'))
                ->description("Jumlah: $jumlahInvoice Invoice"),

            Stat::make('Deposit Masuk (Diterima)', 'Rp ' . number_format($depositMasuk, 0, ',', '.'))
                ->description('Bulan Ini'),
/*
            Stat::make('Deposit Menunggu Konfirmasi', $depositMenunggu . ' transaksi')
                ->description('Belum Dikonfirmasi'),

            Stat::make('Invoice Disetujui', $statusInvoice['disetujui'] . ' Invoice')
                ->description('Status disetujui bulan ini'),

            Stat::make('Invoice Menunggu Persetujuan', $statusInvoice['menunggu'] . ' Invoice')
                ->description('Status menunggu'),

            Stat::make('Invoice Ditolak', $statusInvoice['ditolak'] . ' Invoice')
                ->description('Status ditolak'),
                 */
        ];
    }
}
