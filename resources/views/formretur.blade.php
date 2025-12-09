<div class="container mt-4">
    <h2>Ajukan Retur</h2>

    <?php if (!empty($detail)): ?>
        <div class="alert alert-info">
            <p><strong>Barang:</strong> <?= htmlspecialchars($detail->nama_barang) ?></p>
            <p><strong>Jumlah Dibeli:</strong> <?= htmlspecialchars($detail->jumlah) ?></p>
        </div>

        <form action="/retur" method="POST">
            @csrf
            <input type="hidden" name="id_detail" value="<?= $detail->id_detail ?>">

            <div class="mb-3">
                <label class="form-label">Alasan Retur</label>
                <textarea name="alasan" class="form-control" rows="4" required 
                          placeholder="Jelaskan alasan retur (misal: rusak, salah kirim, dll)"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Ajukan Retur</button>
            <a href="/dashboard" class="btn btn-secondary">Batal</a>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Data tidak ditemukan.</div>
        <a href="/dashboard" class="btn btn-secondary">Kembali</a>
    <?php endif; ?>
</div>