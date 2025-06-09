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

class CreatePermintaanCekKendaraan extends CreateRecord
{
    protected static string $resource = PermintaanCekKendaraanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $user = auth()->user();

        $data['diajukan_oleh'] = $user->id;

        // Jika user adalah supervisor bengkel, isi supervisor_id
        if ($user->role === 'operasional_transportasi') {
            $data['supervisor_id'] = $user->id;
        }

        $laporan = LaporanKendala::find($data['laporan_id']);
        $kendaraanId = PasanganSopirKendaraan::where('driver_id', $laporan->sopir_id)->value('kendaraan_id');

        $data['kendaraan_id'] = $kendaraanId;

        return $data;
    }


    protected function afterCreate(): void
    {
        $permintaan = $this->record;

        // Ambil data laporan kendala
        $laporan = $permintaan->laporan;
        $sopir = optional($laporan?->sopir); // Gunakan optional() untuk menghindari error jika null

        // Kirim notifikasi ke semua supervisor bengkel
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
