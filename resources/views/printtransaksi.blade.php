<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Transaksi</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
            margin: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11pt;
        }

        @media print {
            @page { margin: 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    @if (count($detail) > 0)
        <h3>LAPORAN DETAIL TRANSAKSI</h3>
        <p><strong>ID Transaksi:</strong> {{ htmlspecialchars($detail[0]->id_transaksi) }}</p>
        <p><strong>Nama Pelaku:</strong> {{ htmlspecialchars($detail[0]->nama_pelaku ?? '—') }}</p>
        <p><strong>Tanggal:</strong> {{ htmlspecialchars($detail[0]->tanggal_transaksi) }}</p>
        <p><strong>Total:</strong> Rp {{ number_format($detail[0]->total_harga, 0, ',', '.') }}</p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align:left;">{{ htmlspecialchars($item->nama_barang) }}</td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;">Total Akhir:</td>
                    <td><b>Rp {{ number_format($detail[0]->total_harga, 0, ',', '.') }}</b></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            Dicetak pada {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
        </div>
    @else
        <h3 class="text-center">Transaksi tidak ditemukan!</h3>
    @endif

</body>
</html>
