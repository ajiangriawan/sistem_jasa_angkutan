<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePrintController extends Controller
{
    public function print(Invoice $invoice)
    {
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->stream("Invoice-{$invoice->id}.pdf");
    }
}
