<?php

namespace App\Filament\Resources\LaporanKendalaResource\Pages;

use App\Filament\Resources\LaporanKendalaResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanKendala extends CreateRecord
{
    protected static string $resource = LaporanKendalaResource::class;

    protected function afterCreate(): void
    {
        $laporan = $this->record;

        // Ambil sopir
        $sopir = $laporan->sopir;

        // Tentukan role tujuan berdasarkan kategori
        $targetRole = match ($laporan->kategori) {
            'kerusakan_kendaraan' => 'operasional_transportasi',
            'umum' => 'operasional_pengiriman',
            default => null,
        };

        // Kirim notifikasi jika role valid
        if ($targetRole) {
            $users = User::where('role', $targetRole)->get();

            foreach ($users as $user) {
                Notification::make()
                    ->title('Laporan Kendala Baru')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->body("Sopir {$sopir->name} melaporkan kendala kategori \"{$laporan->kategori}\" pada lokasi: {$laporan->alamat}.")
                    ->actions([
                        NotificationAction::make('Lihat')
                            ->url(LaporanKendalaResource::getUrl('view', ['record' => $laporan]))
                            ->button(),
                    ])
                    ->sendToDatabase($user);
            }
        }
    }
}
