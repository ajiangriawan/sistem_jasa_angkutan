<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Invoice Periode</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .kop {
            text-align: center;
            margin-bottom: 10px;
        }

        .kop img {
            width: 100%;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .info,
        .footer {
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .right-align {
            text-align: right;
        }

        .ttd {
            margin-top: 30px;
            text-align: left;
        }
    </style>
</head>

<body>

    <div class="kop">
        <img src="{{ public_path('kop.png') }}" alt="Logo">
    </div>

    <h3 style="text-align:center;">INVOICE</h3>

    <div class="row">
        <div>
            <p><strong>Tanggal:</strong> {{ $tanggal_invoice }}</p>
            <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }} s.d {{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}</p>
        </div>
        <div>
            <p><strong>Kepada Yth:</strong></p>
            <p>{{ $customer }}</p>
            <p>{{ $customer_alamat }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Keterangan</th>
                <th>QTY (TON)</th>
                <th>Harga/Ton</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
            $rows = [];
            $grandQty = 0;
            $grandTotal = 0;
            @endphp

            @foreach ($invoices as $inv)
            @php
            $rute = $inv->permintaan->rute;
            $harga = $rute->harga ?? 0;
            $pengirimans = $inv->permintaan
            ->jadwalPengiriman
            ->flatMap(fn($jadwal) => $jadwal->detailJadwal)
            ->flatMap(fn($detail) => $detail->pengiriman ? [$detail->pengiriman] : []);

            $qty = $pengirimans->sum('tonase');
            $total = $qty * $harga;

            $rows[] = [
            'nama_rute' => $rute->nama_rute ?? '-',
            'qty' => $qty,
            'harga' => $harga,
            'total' => $total
            ];

            $grandQty += $qty;
            $grandTotal += $total;
            @endphp
            @endforeach

            @foreach ($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['nama_rute'] }}</td>
                <td>{{ number_format($row['qty'], 2, ',', '.') }}</td>
                <td class="right-align">Rp {{ number_format($row['harga'], 0, ',', '.') }}</td>
                <td class="right-align">Rp {{ number_format($row['total'], 2, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr>
                <td></td>
                <td></td>
                <td><strong>{{ number_format($grandQty, 2, ',', '.') }}</strong></td>
                <td class="right-align"><strong>TAGIHAN</strong></td>
                <td class="right-align"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
            </tr>

            @php
            $dpp = round($grandTotal / 1.09);
            $ppn = round($dpp * 0.12);
            $pph = round($dpp * 0.0225);
            $nilaiBayar = $grandTotal + $ppn - $pph;
            @endphp

            <tr>
                <td colspan="4" class="right-align">DPP LAIN</td>
                <td class="right-align">Rp {{ number_format($dpp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="right-align">PPN 12%</td>
                <td class="right-align">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="right-align">PPH 23</td>
                <td class="right-align">Rp {{ number_format($pph, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="right-align"><strong>NILAI BAYAR</strong></td>
                <td class="right-align"><strong>Rp {{ number_format($nilaiBayar, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Pembayaran mohon ditransfer ke rekening:</strong></p>
        <p>Bank: <strong>MANDIRI</strong></p>
        <p>Acc: <strong>113.00.9999252.5</strong></p>
        <p>Atas Nama: <strong>PT. BALINK SAKTI SYNERGY</strong></p>
    </div>

    <div>
        <p>Demikian lah tagihan ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.
        </p>
    </div>

    <div class="ttd">
        <p>Hormat Kami,</p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p>MEIGEN PRAJUDA</p>
        <p>OPERASIONAL MANAGER PT BALINK SAKTY SYNERGY</p>
    </div>

</body>

</html>