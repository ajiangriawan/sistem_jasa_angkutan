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

    public function mount(): void
    {
        parent::mount();

        // Auto-isi permintaan_id jika dikirim melalui query string
        if (request()->has('permintaan_id')) {
            $this->form->fill([
                'permintaan_id' => request()->get('permintaan_id'),
            ]);
        }
    }

    protected function afterCreate(): void
    {
        $jadwal = $this->record;

        $tanggalBerangkatFormatted = Carbon::parse($jadwal->tanggal_berangkat)->format('d M Y');
        $jamBerangkatFormatted = $jadwal->jam_berangkat
            ? Carbon::parse($jadwal->jam_berangkat)->format('H:i')
            : null;

        // =======================
        // === Notifikasi ke Customer ===
        // =======================
        $customerUser = $jadwal->permintaan->customer ?? null;

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

        // =======================
        // === Notifikasi ke Sopir & Update Status ===
        // =======================
        foreach ($jadwal->detailJadwal as $detail) {
            $sopir = $detail->pasangan->sopir ?? null;
            $kendaraan = $detail->pasangan->kendaraan ?? null;

            if ($sopir) {
                Notification::make()
                    ->title('Tugas Pengiriman Baru')
                    ->success()
                    ->icon('heroicon-o-calendar-days')
                    ->body("Anda dijadwalkan untuk pengiriman ke {$customerUser?->name} pada {$tanggalBerangkatFormatted}" . ($jamBerangkatFormatted ? " pukul {$jamBerangkatFormatted}" : "") . ".")
                    ->actions([
                        Action::make('Lihat')
                            ->url(JadwalPengirimanResource::getUrl(name: 'view', parameters: ['record' => $jadwal]))
                            ->button(),
                    ])
                    ->sendToDatabase($sopir);

                $sopir->update([
                    'status' => 'dijadwalkan',
                ]);
            }

            if ($kendaraan) {
                $kendaraan->update([
                    'status' => 'dijadwalkan',
                ]);
            }
        }

        // =======================
        // === Update status permintaan ===
        // =======================
        $jadwal->permintaan->update([
            'status_verifikasi' => 'dijadwalkan',
        ]);
    }
}
