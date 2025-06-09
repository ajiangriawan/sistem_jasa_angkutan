<?php

namespace App\Filament\Resources\JadwalCekKendaraanResource\Pages;

use App\Filament\Resources\JadwalCekKendaraanResource;
use App\Filament\Resources\PermintaanCekKendaraanResource;
use App\Models\User;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalCekKendaraan extends CreateRecord
{
    protected static string $resource = JadwalCekKendaraanResource::class;

    protected function afterCreate(): void
    {
        $jadwal = $this->record;

        // Ambil data permintaan dan laporan
        $permintaan = $jadwal->permintaan;
        $laporan = $permintaan?->laporan;

        $permintaan->update([
            'status'=>'dijadwalkan',
        ]);
        // Ambil teknisi & sopir
        $teknisi = $jadwal->teknisi;
        $sopir = $laporan?->sopir;

        $teknisi->update([
            'status'=>'dijadwalkan',
        ]);

        // 🔔 Notifikasi ke teknisi
        if ($teknisi) {
            Notification::make()
                ->title('Jadwal Cek Kendaraan Baru')
                ->success()
                ->icon('heroicon-o-calendar-days')
                ->body("Anda ditugaskan untuk pengecekan kendaraan milik sopir {$sopir?->name} pada tanggal " . $jadwal->jadwal->format('d M Y H:i') . ".")
                ->actions([
                    NotificationAction::make('Lihat')
                        ->url(JadwalCekKendaraanResource::getUrl('edit', ['record' => $jadwal]))
                        ->button(),
                ])
                ->sendToDatabase($teknisi);
        }

        // 🔔 Notifikasi ke sopir
        if ($sopir) {
            Notification::make()
                ->title('Jadwal Pengecekan Kendaraan Anda')
                ->success()
                ->icon('heroicon-o-wrench-screwdriver')
                ->body("Kendaraan Anda akan dicek oleh teknisi {$teknisi?->name} pada tanggal " . $jadwal->jadwal->format('d M Y H:i') . ".")
                ->sendToDatabase($sopir);
        }

        // 🔔 Notifikasi ke supervisor (opsional, sesuai kebutuhan kamu)
        $supervisors = User::where('role', 'operasional_bengkel')->get();

        foreach ($supervisors as $user) {
            Notification::make()
                ->title('Permintaan Cek Kendaraan Baru')
                ->success()
                ->icon('heroicon-o-wrench-screwdriver')
                ->body("Sopir {$sopir?->name} mengajukan pengecekan kendaraan untuk laporan kendala pada " .
                    $laporan?->created_at->format('d M Y H:i') . ".")
                ->actions([
                    NotificationAction::make('Lihat')
                        ->url(PermintaanCekKendaraanResource::getUrl('edit', ['record' => $permintaan]))
                        ->button(),
                ])
                ->sendToDatabase($user);
        }
    }
}
