<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewDeposit extends ViewRecord
{
    protected static string $resource = DepositResource::class;

    protected function authorizeAccess(): void
    {
        if (Auth::user()->role === 'customer' && Auth::id() !== $this->record->user_id) {
            abort(403);
        }
    }
}
