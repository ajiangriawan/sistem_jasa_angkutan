<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Actions;

class EditDeposit extends EditRecord
{
    protected static string $resource = DepositResource::class;

     protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        // Hanya customer yang membuat deposit yang bisa edit
        if (Auth::user()->role === 'customer' && Auth::id() !== $this->record->user_id) {
            abort(403);
        }
    }
}

