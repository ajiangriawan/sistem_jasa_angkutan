<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoicePrintController;
use App\Http\Controllers\LaporanPengirimanController;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use App\Exports\LaporanPengirimanExport;
use Maatwebsite\Excel\Facades\Excel;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/admin/invoices/print-periode', function () {
    $start = Carbon::parse(request('start_date'))->startOfDay();
    $end = Carbon::parse(request('end_date'))->endOfDay();

    $invoices = Invoice::with(['permintaan.rute', 'customer'])
        ->whereBetween('created_at', [$start, $end])
        ->get();

    if ($invoices->isEmpty()) {
        abort(404, 'Tidak ada data invoice dalam periode tersebut.');
    }

    $pdf = Pdf::loadView('pdf.invoices-periode', [
        'invoices' => $invoices,
        'start_date' => request('start_date'),
        'end_date' => request('end_date'),
    ])->setPaper('a4', 'portrait');

    return $pdf->download('invoice-periode-' . now()->format('YmdHis') . '.pdf');
});

Route::get('/invoices/{invoice}/print', [InvoicePrintController::class, 'print'])->name('invoices.print');


Route::get('/laporan-export', function () {
    $start = request('tanggal_awal');
    $end = request('tanggal_akhir');
    $customerId = request('customer_id');

    return Excel::download(new LaporanPengirimanExport($start, $end, $customerId), 'laporan_pengiriman.xlsx');
})->name('laporan.export');
