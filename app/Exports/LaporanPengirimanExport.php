<?php

namespace App\Exports;

use App\Models\Permintaan;
use App\Models\Deposit;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPengirimanExport implements FromArray, WithHeadings
{
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
{
    $rows = [];

    $permintaans = Permintaan::whereBetween('updated_at', [$this->startDate, $this->endDate])
        ->where('status_verifikasi', 'selesai')
        ->with([
            'rute',
            'customer',
            'jadwalPengiriman.detailJadwal.pasangan.sopir',
            'jadwalPengiriman.detailJadwal.pasangan.kendaraan',
        ])
        ->get();

    foreach ($permintaans as $permintaan) {
        foreach ($permintaan->jadwalPengiriman as $jadwal) {
            foreach ($jadwal->detailJadwal as $detail) {
                $sopir = $detail->pasangan->sopir?->name;
                $unit = $detail->pasangan->kendaraan?->no_polisi;
                $tonase = $permintaan->pengirimanTonase->sum('tonase') / $jadwal->detailJadwal->count(); // rata-rata

                if (!$sopir && !$unit) continue;
                $customer = $permintaan->customer;

                $depositAmount = Deposit::where('user_id', $customer?->id)
                    ->where('status', 'diterima') // sesuaikan dengan status valid di sistemmu
                    ->latest()
                    ->value('jumlah') ?? 0;

                $uangJalan = $permintaan->rute->uang_jalan ?? 0;
                $bonusPerTon = $permintaan->rute->bonus ?? 0;

                $tonaseLebih = max(0, $tonase - 30);
                $totalBonus = $tonaseLebih * $bonusPerTon;
                $jumlah = $depositAmount - ($uangJalan + $totalBonus);

                $rows[] = [
                    $permintaan->updated_at->format('d-m-Y'),
                    $permintaan->rute->nama_rute ?? '-',
                    $sopir,
                    $unit,
                    number_format($tonase, 2, ',', '.'),
                    'Rp ' . number_format($depositAmount, 0, ',', '.'),
                    'Rp ' . number_format($uangJalan, 0, ',', '.'),
                    'Rp ' . number_format($totalBonus, 0, ',', '.'),
                    'Rp ' . number_format($jumlah, 0, ',', '.'),
                ];
            }
        }
    }

    return $rows;
}


    public function headings(): array
    {
        return [
            'Tanggal Selesai',
            'Rute',
            'Nama Sopir',
            'Nomor Plat',
            'Tonase (Ton)',
            'Deposit',
            'Uang Jalan',
            'Bonus Tonase',
            'Jumlah',
        ];
    }
}
