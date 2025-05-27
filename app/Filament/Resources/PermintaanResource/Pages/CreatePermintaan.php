<?php

namespace App\Filament\Resources\PermintaanResource\Pages;

use App\Filament\Resources\PermintaanResource;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePermintaan extends CreateRecord
{
    protected static string $resource = PermintaanResource::class;

    protected function afterCreate(): void
    {
        $admins = User::where('role', 'admin')->get();
        $permintaan = $this->record;

        foreach ($admins as $admin) {
            Notification::make()
                ->title('Permintaan Pengiriman Baru')
                ->success()
                ->icon('heroicon-o-document-text')
                ->body("Permintaan pengiriman dari {$permintaan->customer->nama_perusahaan} telah dibuat.")
                ->actions([
                    Action::make('Lihat')
                        ->url(PermintaanResource::getUrl(name: 'view', parameters: ['record' => $permintaan]))
                        ->button(),
                ])
                ->sendToDatabase($admin);
        }
    }
}
