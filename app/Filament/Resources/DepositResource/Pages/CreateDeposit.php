<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    protected function afterCreate(): void
    {
        $deposit = $this->record;
        $customer = $deposit->user;

        // Cari semua user dengan role akuntan
        $akuntans = User::where('role', 'akuntan')->get();

        foreach ($akuntans as $akuntan) {
            Notification::make()
                ->title('Deposit Baru Diajukan')
                ->success()
                ->icon('heroicon-o-banknotes')
                ->body("Customer {$customer->name} telah mengajukan deposit sebesar Rp " . number_format($deposit->jumlah, 0, ',', '.') . ".")
                ->actions([
                    Action::make('Lihat')
                        ->url(DepositResource::getUrl(name: 'view', parameters: ['record' => $deposit]))
                        ->button(),
                ])
                ->sendToDatabase($akuntan);
        }
    }
}
