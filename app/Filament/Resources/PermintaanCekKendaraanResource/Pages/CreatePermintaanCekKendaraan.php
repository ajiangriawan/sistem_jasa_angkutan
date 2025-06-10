<?php

namespace App\Filament\Resources\PermintaanCekKendaraanResource\Pages;

use App\Filament\Resources\PermintaanCekKendaraanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;
use App\Models\LaporanKendala;
use App\Models\PasanganSopirKendaraan;
use Filament\Forms\Get; // Tambahkan ini untuk akses ke nilai form

class CreatePermintaanCekKendaraan extends CreateRecord
{
    protected static string $resource = PermintaanCekKendaraanResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();

        if (request()->has('laporan_id')) {
            $this->form->fill([
                'laporan_id' => request()->get('laporan_id'),
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan laporan_id terisi dari request jika ada.
        // Seharusnya ini sudah ditangani oleh fillForm dan live field,
        // tapi ini sebagai fallback atau untuk direct submission.
        if (request()->has('laporan_id')) {
            $data['laporan_id'] = request()->get('laporan_id');
        }

        $user = auth()->user();
        $data['diajukan_oleh'] = $user->id;

        if ($user->role === 'operasional_transportasi') {
            $data['supervisor_id'] = $user->id;
        }

        $laporan = LaporanKendala::find($data['laporan_id']);
        $kendaraanId = PasanganSopirKendaraan::where('driver_id', $laporan->sopir_id)->value('kendaraan_id');

        $data['kendaraan_id'] = $kendaraanId;
        // Jika kendaraanId masih null setelah semua pencarian,
        // itu berarti ada masalah dan kita harus menghentikan proses atau memberi tahu pengguna.
        if (is_null($kendaraanId)) {
            Notification::make()
                ->title('Gagal Membuat Permintaan')
                ->danger()
                ->body('Tidak dapat menemukan kendaraan terkait dengan laporan kendala yang dipilih. Pastikan sopir memiliki pasangan kendaraan yang terdaftar.')
                ->persistent()
                ->send();

            // Ini akan menghentikan proses pembuatan record
            $this->halt();
        }

        $data['kendaraan_id'] = $kendaraanId; // Set kendaraan_id setelah dipastikan tidak null

        return $data;
    }


    protected function afterCreate(): void
    {
        $permintaan = $this->record;

        $laporan = $permintaan->laporan;
        $sopir = optional($laporan?->sopir);

        $supervisors = User::where('role', 'operasional_bengkel')->get();

        foreach ($supervisors as $user) {
            Notification::make()
                ->title('Permintaan Cek Kendaraan Baru')
                ->success()
                ->icon('heroicon-o-wrench-screwdriver')
                ->body(
                    "Sopir {$sopir?->name} mengajukan pengecekan kendaraan untuk laporan kendala pada " .
                        $laporan?->created_at->format('d M Y H:i') . "."
                )
                ->actions([
                    Action::make('Lihat')
                        ->url(PermintaanCekKendaraanResource::getUrl('edit', ['record' => $permintaan]))
                        ->button(),
                ])
                ->sendToDatabase($user);
        }
    }
}
