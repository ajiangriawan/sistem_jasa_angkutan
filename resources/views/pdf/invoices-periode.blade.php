<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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

        .info,
        .footer {
            margin: 10px 0;
            font-size: 11px;
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

        .terbilang {
            margin-top: 10px;
            font-style: italic;
        }

        .footer p {
            margin: 0;
        }

        .ttd {
            margin-top: 30px;
            text-align: right;
        }

        .ttd img {
            height: 100px;
        }

        .column {
            float: left;
            width: 50%;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>

    <div class="kop">
        <img src="{{ public_path('kop.png') }}" alt="Logo">
    </div>

    <h3 style="text-align:center; margin: bottom 4px;">INVOICE</h3>

    <div class="row">
        <div class="column">
            <p><Strong>Tanggal:</Strong>{{$tanggal_invoice}}</p>
            <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }} s.d {{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}</p>
        </div>
        <div class="column"></div>
        <div class="column">
            <Strong>Kepada Yth:</Strong>
            <p>{{ $customer }}<br>
            <p>{{ $customer_alamat }}<br>
            </p>
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
            $subtotal = 0;
            @endphp
            @foreach ($invoices as $i => $inv)
            @php
            $qtysopir = $inv->permintaan->detailJadwalPengirimans->count();
            $uangjalan = $inv->permintaan->rute->uang_jalan ?? 0;

            $pengirimans = $inv->permintaan
            ->detailJadwalPengirimans
            ->load('pengirimans') // pastikan relasi diload
            ->flatMap(fn ($detail) => $detail->pengirimans);

            $qty = $pengirimans->sum('tonase');
            $harga = $inv->permintaan->rute->harga?? 0;
            $total = $qty * $harga;
            $subtotal += $total;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $inv->permintaan->rute->nama_rute ?? '-' }}</td>
                <td>{{ $qty }}</td>
                <td class="right-align">Rp{{ number_format($harga, 0, ',', '.') }}</td>
                <td class="right-align">Rp{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4"><strong>SUB TOTAL</strong></td>
                <td class="right-align"><strong>Rp{{ number_format($subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @php
            $pph = $subtotal * 0.005;
            $totalTagihan = $subtotal - $pph;
            @endphp
            <tr>
                <td colspan="4">PPH Final 0.5%</td>
                <td class="right-align">Rp{{ number_format($pph, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4"><strong>TOTAL TAGIHAN</strong></td>
                <td class="right-align"><strong>Rp{{ number_format($totalTagihan, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    <div class="footer">
        <p><strong>Pembayaran mohon ditransfer ke rekening:</strong></p>
        <p>Bank: <strong>MANDIRI</strong></p>
        <p>Acc: <strong>113.00.9999252.5</strong></p>
        <p>Atas Nama: <strong>PT. BALINK SAKTI SYNERGY</strong></p>
    </div>

    <div class="ttd">
        <p style="margin-bottom: 10px;">Hormat Kami,</p>
        <!--<img src="{{ public_path('stempel-tanda-tangan.png') }}" alt="Stempel dan TTD">-->
        <p>Andi Seman</p>
    </div>

</body>

</html>