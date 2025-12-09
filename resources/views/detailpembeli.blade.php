<div class="container mt-4">
    <h2>Detail Transaksi</h2>
    <a href="/dashboard" class="btn btn-secondary mb-3">Kembali</a>

    <?php 
    if (!empty($detail)) {
        $first = $detail[0];
    ?>
        <p><strong>Pelaku:</strong> <?= htmlspecialchars($first->nama_pelaku) ?></p>
        <p><strong>Tanggal:</strong> <?= htmlspecialchars($first->tanggal_transaksi) ?></p>
        <p><strong>Total:</strong> Rp <?= number_format($first->total_harga, 0, ',', '.') ?></p>

        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($detail as $d) {
                    $subtotal = $d->harga * $d->jumlah;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($d->nama_barang) ?></td>
                        <td>Rp <?= number_format($d->harga, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($d->jumlah) ?></td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        <td>
                            <?php 
                            if (session('role') == 'pembeli') {

                                // Cek apakah sudah diretur
                                $returExists = DB::table('retur')
                                    ->where('id_detail', $d->id_detail)
                                    ->exists();

                                if ($returExists) {
                                    echo '<span class="badge bg-info">Retur Diajukan</span>';
                                } else {
                                    echo '<a href="/retur/create/' . $d->id_detail . '" class="btn btn-warning btn-sm">Ajukan Retur</a>';
                                }
                            } else {
                                echo '<span class="text-muted">Tidak tersedia</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php 
    } else { 
    ?>
        <div class="alert alert-warning">Transaksi tidak ditemukan.</div>
    <?php } ?>
</div>
