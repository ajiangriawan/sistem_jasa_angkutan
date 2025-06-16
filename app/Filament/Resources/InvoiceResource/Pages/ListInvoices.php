<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('+ Uang Jalan'),

            Action::make('cetakPeriode')
                ->label('Cetak Invoice Pengiriman')
                ->icon('heroicon-o-printer')
                ->url(InvoiceResource::getUrl('cetak-periode')),
        ];
    }
}
