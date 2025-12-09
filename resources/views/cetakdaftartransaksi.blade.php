<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Daftar Transaksi</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <style>
        @media print {
            @page { margin: 15mm; }
            .btn, .no-print { display: none !important; }
        }
        body {
            font-family: "Times New Roman", serif;
            color: #000;
        }
        h3, h4 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .line {
            border-bottom: 2px solid #000;
            margin: 10px 0 20px 0;
        }
        .table th, .table td {
            font-size: 13px;
            vertical-align: middle;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container mt-4">
        <h3>LAPORAN KEUANGAN</h3>
        <h4>DAFTAR TRANSAKSI PENJUALAN ALAT MUSIK</h4>
        <div class="line"></div>

        <?php if (!empty($transaksi) && count($transaksi) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Pembeli / Pelaku</th>
                            <th>Jumlah Jenis Barang</th>
                            <th>Total Harga (Rp)</th>
                            <th>Tanggal Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        $totalSemua = 0;
                        foreach ($transaksi as $t) { 
                            $totalSemua += $t->total_harga;
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($t->nama_pelaku) ?></td>
                            <td class="text-center"><?= $t->jumlah_jenis_barang ?></td>
                            <td class="text-end">Rp <?= number_format($t->total_harga, 0, ',', '.') ?></td>
                            <td class="text-center"><?= date('d-m-Y', strtotime($t->tanggal_transaksi)) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-center">Total Keseluruhan</th>
                            <th class="text-end">Rp <?= number_format($totalSemua, 0, ',', '.') ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center">Tidak ada transaksi ditemukan.</div>
        <?php } ?>

        <div class="signature">
            <p>Batam, <?= date('d M Y') ?></p>
            <br><br><br>
            <p><strong><?= htmlspecialchars(session('nama_user')) ?></strong></p>
        </div>
    </div>
</body>
</html>
