<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPengirimanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPengirimanController extends Controller
{
    public function export(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        if (!$startDate || !$endDate) {
            return back()->with('error', 'Tanggal belum lengkap. Silakan isi tanggal mulai dan tanggal akhir terlebih dahulu.');
        }

        return Excel::download(new LaporanPengirimanExport($startDate, $endDate), 'laporan_pengiriman.xlsx');
    }
}
