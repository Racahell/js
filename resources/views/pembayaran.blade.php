<div class="container mt-4">
    <div class="text-center mb-4">
        <h2>STRUK PEMBAYARAN</h2>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Detail Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>No. Transaksi:</strong> #<?= $transaksi[0]->id_transaksi ?></p>
                    <p><strong>Tanggal:</strong> <?= $transaksi[0]->tanggal_transaksi ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Pembeli:</strong> <?= session('nama_user') ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-success">LUNAS</span></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($transaksi as $index => $t){ ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($t->nama_barang) ?></td>
                            <td>Rp <?= number_format($t->harga_satuan, 0, ',', '.') ?></td>
                            <td><?= $t->jumlah ?></td>
                            <td>Rp <?= number_format($t->harga_satuan * $t->jumlah, 0, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-success">
                            <th colspan="4" class="text-end">TOTAL BAYAR</th>
                            <th>Rp <?= number_format($transaksi[0]->total_harga, 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
        <a href="/halpembeli" class="btn btn-primary">Belanja Lagi</a>
        <a href="/keranjang" class="btn btn-outline-secondary">Lihat Keranjang</a>
    </div>
</div>