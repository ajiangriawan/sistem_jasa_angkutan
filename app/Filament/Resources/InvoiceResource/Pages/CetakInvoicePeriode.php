<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class CetakInvoicePeriode extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.cetak-invoice-periode';

    public $start_date;
    public $end_date;
    public $customer_id;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('customer_id')
                ->label('Pilih Customer')
                ->options(User::where('role', 'customer')->pluck('name', 'id'))
                ->required(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Dari Tanggal')
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Sampai Tanggal')
                ->required(),
        ];
    }

    public function print()
    {
        $invoices = Invoice::where('customer_id', $this->customer_id)
            ->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay(),
            ])
            ->with(['customer', 'permintaan.rute']) // after filter
            ->get();

        if ($invoices->isEmpty()) {
            Notification::make()
                ->title('Tidak ada invoice untuk customer & tanggal tersebut.')
                ->danger()
                ->send();

            return;
        }

        $tanggalCetak = Carbon::now();

        $pdf = Pdf::loadView('pdf.invoices-periode', [
            'invoices' => $invoices,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'tanggal_invoice' => $tanggalCetak->format('d-m-Y'),
            'customer' => $invoices->first()->customer->name,
            'customer_alamat' => $invoices->first()->customer->alamat,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'invoice-periode-' . now()->format('YmdHis') . '.pdf');
    }
}
