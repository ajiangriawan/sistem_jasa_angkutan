<?php

namespace App\Exports;

use App\Models\Deposit;
use App\Models\Permintaan;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPengirimanExport implements FromArray, WithHeadings
{
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected ?int $customerId = null;

    public function __construct(string $startDate, string $endDate, ?int $customerId = null)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        $this->customerId = $customerId;
    }

    public function array(): array
    {
        $rows = [];

        $totalTonase = 0;
        $totalDebit = 0;
        $totalKredit = 0;
        $totalBonus = 0;
        $totalJumlah = 0;

        $deposits = Deposit::with('user')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->when($this->customerId, fn($q) => $q->where('user_id', $this->customerId))
            ->where('status', 'diterima')
            ->orderBy('created_at')
            ->get();

        foreach ($deposits as $deposit) {
            $date = Carbon::parse($deposit->created_at)->format('d-m-Y');
            $customer = $deposit->user;
            $amount = $deposit->jumlah;

            $debit = $amount > 0 ? $amount : 0;
            $kredit = $amount < 0 ? abs($amount) : 0;
            $info = $amount > 0 ? 'Deposit dari customer' : 'Pembayaran uang jalan / bonus';

            $rute = '-';
            $sopir = '-';
            $plat = '-';
            $tonase = '-';
            $bonus = 0;

            if ($amount < 0) {
                $permintaan = Permintaan::where('customer_id', $customer->id)
                    ->whereDate('updated_at', '<=', $deposit->created_at)
                    ->latest('updated_at')
                    ->with([
                        'rute',
                        'pengirimanTonase',
                        'jadwalPengiriman.detailJadwal.pasangan.sopir',
                        'jadwalPengiriman.detailJadwal.pasangan.kendaraan',
                    ])
                    ->first();

                if ($permintaan) {
                    $rute = $permintaan->rute->nama_rute ?? '-';
                    $bonusPerTon = $permintaan->rute->bonus ?? 0;

                    $detail = optional($permintaan->jadwalPengiriman->first())->detailJadwal->first();
                    if ($detail && $detail->pasangan) {
                        $sopir = $detail->pasangan->sopir->name ?? '-';
                        $plat = $detail->pasangan->kendaraan->no_polisi ?? '-';
                    }

                    $pengirimanList = $permintaan->pengirimanTonase
                        ->whereBetween('tanggal', [$this->startDate, $this->endDate]);

                    foreach ($pengirimanList as $pengiriman) {
                        $tonaseVal = $pengiriman->tonase ?? 0;
                        $bonusVal = $tonaseVal > 30 ? ($tonaseVal - 30) * $bonusPerTon : 0;
                        $tanggal = Carbon::parse($pengiriman->tanggal)->format('d-m-Y');

                        $rows[] = [
                            $tanggal,
                            $customer->name ?? '-',
                            'Pembayaran uang jalan / bonus',
                            $rute,
                            $sopir,
                            $plat,
                            number_format($tonaseVal, 2, ',', '.'),
                            '',
                            'Rp ' . number_format($kredit, 0, ',', '.'),
                            'Rp ' . number_format($bonusVal, 0, ',', '.'),
                            'Rp ' . number_format($amount, 0, ',', '.'),
                        ];

                        $totalTonase += $tonaseVal;
                        $totalKredit += $kredit;
                        $totalBonus += $bonusVal;
                        $totalJumlah += $amount;
                    }

                    continue;
                }
            }

            // Untuk deposit masuk
            $rows[] = [
                $date,
                $customer->name ?? '-',
                $info,
                $rute,
                $sopir,
                $plat,
                $tonase,
                $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '',
                '',
                '',
                'Rp ' . number_format($amount, 0, ',', '.'),
            ];

            $totalDebit += $debit;
            $totalJumlah += $amount;
        }

        if (!empty($rows)) {
            $rows[] = [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                number_format($totalTonase, 2, ',', '.'),
                'Rp ' . number_format($totalDebit, 0, ',', '.'),
                'Rp ' . number_format($totalKredit, 0, ',', '.'),
                'Rp ' . number_format($totalBonus, 0, ',', '.'),
                'Rp ' . number_format($totalJumlah, 0, ',', '.'),
            ];
        } else {
            $rows[] = ['Tidak ada data transaksi pada rentang waktu yang dipilih.'];
        }

        return $rows;
    }


    public function headings(): array
    {
        return [
            'Tanggal',
            'Customer',
            'Keterangan',
            'Rute',
            'Sopir',
            'No. Plat',
            'Tonase (Ton)',
            'DEBIT (Deposit Masuk)',
            'KREDIT (Uang Jalan)',
            'KREDIT (Bonus Tonase)',
            'Jumlah (Sisa/Minus)',
        ];
    }
}
