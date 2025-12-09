<div class="container mt-4">
    <h2>Daftar Transaksi Kasir</h2>

    <div class="mb-3">
        <a href="/dashboard" class="btn btn-secondary">Kembali</a>
    </div>

    <?php if ($transaksi->isEmpty()) { ?>
        <div class="alert alert-info">Belum ada transaksi yang Anda proses.</div>
    <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Nama Pembeli</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $t) { ?>
                        <tr>
                            <td><?= htmlspecialchars($t->id_transaksi) ?></td>
                            <td><?= htmlspecialchars($t->nama_pembeli ?? '-') ?></td>
                            <td><?= date('d-m-Y', strtotime($t->tanggal_transaksi)) ?></td>
                            <td>Rp <?= number_format($t->total_harga, 0, ',', '.') ?></td>
                            <td>
                                <a href="/printtransaksi/<?= $t->id_transaksi ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">
                                   Cetak Struk
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>
