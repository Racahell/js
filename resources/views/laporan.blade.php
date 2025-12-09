<div class="container mt-4">
    <h2 class="mb-3 text-center">LAPORAN KEUANGAN TRANSAKSI</h2>
    <p class="text-muted text-center"><em>Dihasilkan pada: <?= date('d F Y') ?></em></p>
    <hr>

    <?php if (!empty($totalPendapatan)) { ?>
        <div style="text-align: left; margin-bottom: 15px;">
            <a href="/keuangan/export-pdf" class="btn btn-export-pdf" target="_blank">
                Export PDF
            </a>
            <a href="/keuangan/export-excel" class="btn btn-export-excel">
                Export Excel
            </a>
        </div>
        <p><strong>Total Pendapatan:</strong> Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
    <?php } else { ?>
        <div class="alert alert-warning">Belum ada transaksi untuk dihitung.</div>
    <?php } ?>

    <?php if (!empty($pendapatanPerTanggal)) { ?>
        <h4 class="mt-4 mb-3">Pendapatan per Tanggal</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>Pendapatan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendapatanPerTanggal as $tanggal => $jumlah) { ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($tanggal)) ?></td>
                            <td class="text-end"><?= number_format($jumlah, 0, ',', '.') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>
