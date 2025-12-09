<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-height: 60px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 10px 0;
            font-size: 20px;
        }
        .generated {
            text-align: center;
            font-style: italic;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="logo">
        <strong><img src="{{ asset('images/Asset 3.png') }}" alt="Logo"  class="rounded me-2"></strong>
    </div>

    <div class="header">
        <h1>LAPORAN KEUANGAN TRANSAKSI</h1>
    </div>

    <div class="generated">
        Dihasilkan pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
    </div>

    <div class="summary">
        <strong>Ringkasan</strong><br>
        Total Pendapatan: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
    </div>

    <h3>Pendapatan per Tanggal</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendapatanPerTanggal as $tanggal => $jumlah)
            <tr>
                <td>{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</td>
                <td class="text-right">{{ number_format($jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>