<?php

namespace App\Filament\Resources\JadwalPengirimanResource\Pages;

use App\Filament\Resources\JadwalPengirimanResource;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateJadwalPengiriman extends CreateRecord
{
    protected static string $resource = JadwalPengirimanResource::class;
    protected function afterCreate(): void
    {
        $jadwal = $this->record;

        $tanggalBerangkatFormatted = \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->format('d M Y');
        $jamBerangkatFormatted = $jadwal->jam_berangkat
            ? \Carbon\Carbon::parse($jadwal->jam_berangkat)->format('H:i')
            : null;

        // Notifikasi ke customer
        $customerUser = $jadwal->permintaan->customer->user ?? null;
        if ($customerUser) {
            Notification::make()
                ->title('Jadwal Pengiriman Telah Dibuat')
                ->success()
                ->icon('heroicon-o-truck')
                ->body("Pengiriman untuk rute {$jadwal->permintaan->rute->nama_rute} dijadwalkan pada {$tanggalBerangkatFormatted}" . ($jamBerangkatFormatted ? " pukul {$jamBerangkatFormatted}" : "") . ".")
                ->actions([
                    Action::make('Lihat')
                        ->url(JadwalPengirimanResource::getUrl(name: 'view', parameters: ['record' => $jadwal]))
                        ->button(),
                ])
                ->sendToDatabase($customerUser);
        }

        // Notifikasi ke sopir
        $driverUser = $jadwal->sopir->user ?? null;
        if ($driverUser) {
            Notification::make()
                ->title('Tugas Pengiriman Baru')
                ->success()
                ->icon('heroicon-o-calendar-days')
                ->body("Anda dijadwalkan untuk mengantar pengiriman ke {$jadwal->permintaan->customer->nama_perusahaan} pada {$tanggalBerangkatFormatted}" . ($jamBerangkatFormatted ? " pukul {$jamBerangkatFormatted}" : "") . ".")
                ->actions([
                    Action::make('Lihat')
                        ->url(JadwalPengirimanResource::getUrl(name: 'view', parameters: ['record' => $jadwal]))
                        ->button(),
                ])
                ->sendToDatabase($driverUser);
        }

        // Ubah status permintaan, kendaraan, dan sopir jadi 'dijadwalkan'
        $jadwal->permintaan->update([
            'status_verifikasi' => 'dijadwalkan',
        ]);

        $jadwal->kendaraan?->update([
            'status' => 'dijadwalkan',
        ]);

        $jadwal->sopir?->update([
            'status' => 'dijadwalkan',
        ]);
    }
}
