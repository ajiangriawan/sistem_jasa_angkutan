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
        $permintaan = Permintaan::with(['rute', 'detailJadwalPengirimans'])->find($data['permintaan_id']);

        if (!$permintaan) {
            return $data;
        }

        $uangJalan = $permintaan->rute->uang_jalan ?? 0;
        $jumlahUnitTerkirim = $permintaan->detailJadwalPengirimans->count();

        $total = $uangJalan * $jumlahUnitTerkirim;

        $data['total_uang_jalan'] = $total;

        $totalDeposit = Deposit::where('user_id', $permintaan->customer_id)
            ->where('status', 'diterima')
            ->sum('jumlah');

        $sisa = $totalDeposit - $total;

        $data['sisa_deposit_setelah_invoice'] = $sisa;
        $data['customer_id'] = $permintaan->customer_id;

        // Catat pengurangan deposit hanya jika deposit cukup atau saldo minus diizinkan
        Deposit::create([
            'user_id' => $permintaan->customer_id,
            'jumlah' => -1 * $total,
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
                'jadwalPengiriman.detailJadwal.pasangan.sopir'
            ])->find($permintaanId);

            if ($permintaan) {
                $uangJalan = $permintaan->rute->uang_jalan ?? 0;
                $jumlahUnitTerkirim = $permintaan->jadwalPengiriman
                    ?->flatMap(fn($jadwal) => $jadwal->detailJadwal)->count() ?? 0;

                $total = $uangJalan * $jumlahUnitTerkirim;

                $this->form->fill([
                    'permintaan_id' => $permintaan->id,
                    'customer_id' => $permintaan->customer_id,
                    'total_uang_jalan' => $total,
                ]);
            }
        }

        return parent::getFormModel();
    }
}
