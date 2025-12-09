
    <div class="container mt-4">
        <?php
        $isEdit = isset($barang);
        ?>

        <h2><?= $isEdit ? 'Edit Barang' : 'Tambah Barang Baru' ?></h2>

        <form action="<?= $isEdit ? "/barang/{$barang->id_barang}" : '/barang' ?>" method="POST">
            @csrf
            <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Kode Barang:</label>
                <input type="text" name="kode_barang" 
                       class="form-control"
                       value="<?= old('kode_barang', $isEdit ? $barang->kode_barang : '') ?>" 
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Barang:</label>
                <input type="text" name="nama_barang" 
                       class="form-control"
                       value="<?= old('nama_barang', $isEdit ? $barang->nama_barang : '') ?>" 
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis:</label>
                <input type="text" name="jenis" 
                       class="form-control"
                       value="<?= old('jenis', $isEdit ? $barang->jenis : '') ?>" 
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga:</label>
                <input type="number" name="harga" 
                       class="form-control"
                       value="<?= old('harga', $isEdit ? $barang->harga : '') ?>" 
                       min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Stok:</label>
                <input type="number" name="stok" 
                       class="form-control"
                       value="<?= old('stok', $isEdit ? $barang->stok : '') ?>" 
                       min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Masuk:</label>
                <input type="date" name="tanggal" 
                       class="form-control"
                       value="<?= old('tanggal', $isEdit ? $barang->tanggal : date('Y-m-d')) ?>" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Simpan' ?></button>
            <a href="/halbarang" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    