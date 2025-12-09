<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .filter { margin-bottom: 15px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Transaksi</h2>
        @if($tanggal_awal || $tanggal_akhir)
            <div class="filter">
                Periode: 
                {{ $tanggal_awal ? \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') : 'Awal' }}
                s/d 
                {{ $tanggal_akhir ? \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') : 'Akhir' }}
            </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Pelaku</th>
                <th>Total Harga</th>
                <th>Jenis Barang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $t)
                <tr>
                    <td>{{ $t->id_transaksi }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d-m-Y') }}</td>
                    <td>{{ $t->nama_pelaku }}</td>
                    <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $t->jumlah_jenis_barang }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>