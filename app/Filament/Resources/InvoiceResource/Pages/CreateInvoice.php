<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Permintaan;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $permintaan = Permintaan::with([
            'rute',
            'jadwalPengiriman.detailJadwal.pengiriman',
            'jadwalPengiriman.detailJadwal.pasangan.sopir',
        ])->find($data['permintaan_id']);

        if (!$permintaan) {
            return $data;
        }

        $uangJalan = $permintaan->rute->uang_jalan ?? 0;
        $bonusPerTon = $permintaan->rute->bonus ?? 0;

        $totalUangJalan = 0;

        foreach ($permintaan->jadwalPengiriman as $jadwal) {
            foreach ($jadwal->detailJadwal as $detail) {
                $tonase = $detail->pengiriman->tonase ?? 0;

                // Hitung bonus per pengiriman jika tonase > 30
                $bonus = max(0, $tonase - 30) * $bonusPerTon;

                $totalUangJalan += $uangJalan + $bonus;
            }
        }

        $data['total_uang_jalan'] = $totalUangJalan;

        $totalDeposit = Deposit::where('user_id', $permintaan->customer_id)
            ->where('status', 'diterima')
            ->sum('jumlah');

        $data['sisa_deposit_setelah_invoice'] = $totalDeposit - $totalUangJalan;
        $data['customer_id'] = $permintaan->customer_id;

        Deposit::create([
            'user_id' => $permintaan->customer_id,
            'jumlah' => -1 * $totalUangJalan,
            'status' => 'diterima',
            'catatan' => 'Potongan invoice dari permintaan ID: ' . $permintaan->id,
        ]);

        return $data;
    }


    protected function getFormModel(): Model|string|null
    {
        $permintaanId = request()->get('permintaan_id');

        if ($permintaanId) {
            $permintaan = Permintaan::with([
                'rute',
                'jadwalPengiriman.detailJadwal.pengiriman',
            ])->find($permintaanId);

            if ($permintaan) {
                $uangJalan = $permintaan->rute->uang_jalan ?? 0;
                $bonusPerTon = $permintaan->rute->bonus ?? 0;

                $totalUangJalan = 0;

                foreach ($permintaan->jadwalPengiriman as $jadwal) {
                    foreach ($jadwal->detailJadwal as $detail) {
                        $tonase = $detail->pengiriman->tonase ?? 0;
                        $bonus = max(0, $tonase - 30) * $bonusPerTon;
                        $totalUangJalan += $uangJalan + $bonus;
                    }
                }

                $this->form->fill([
                    'permintaan_id' => $permintaan->id,
                    'customer_id' => $permintaan->customer_id,
                    'total_uang_jalan' => $totalUangJalan,
                ]);
            }
        }

        return parent::getFormModel();
    }
}
