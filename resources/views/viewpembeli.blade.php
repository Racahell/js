<div class="container mt-4">
    <h2 class="mb-4 text-center text-primary">Daftar Barang Tersedia</h2>

    <div class="row">
        <?php foreach ($barang as $b) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-success fw-bold">
                            <?= htmlspecialchars($b->nama_barang) ?>
                        </h5>
                        <p class="card-text mb-1">
                            <strong>Jenis:</strong> <?= htmlspecialchars($b->jenis) ?><br>
                            <strong>Harga:</strong> Rp <?= number_format($b->harga, 0, ',', '.') ?><br>
                            <strong>Stok:</strong> <?= htmlspecialchars($b->stok) ?>
                        </p>
                        <form action="/keranjang/tambah" method="POST">
                            @csrf
                            <input type="hidden" name="id_barang" value="<?= $b->id_barang ?>">

                            <div class="mb-3">
                                <label for="jumlah_<?= $b->id_barang ?>" class="form-label">Jumlah</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="jumlah_<?= $b->id_barang ?>" 
                                    name="jumlah" 
                                    value="1" 
                                    min="1" 
                                    max="<?= $b->stok ?>" 
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
