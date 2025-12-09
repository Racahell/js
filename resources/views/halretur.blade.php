<div class="container mt-4">
    <h2>Manajemen Retur</h2>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Barang</th>
                    <th>Tgl Retur</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($retur)): ?>
                    <?php foreach ($retur as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r->id_retur) ?></td>
                        <td><?= htmlspecialchars($r->nama_pelanggan ?? '—') ?></td>
                        <td><?= htmlspecialchars($r->nama_barang) ?></td>
                        <td><?= htmlspecialchars($r->tanggal_retur) ?></td>
                        <td><?= htmlspecialchars($r->alasan) ?></td>
                        <td>
                            <?php
                            $badgeClass = match($r->status) {
                                'disetujui' => 'bg-success',
                                'ditolak' => 'bg-danger',
                                default => 'bg-warning'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($r->status) ?></span>
                        </td>
                        <td>
                            <?php if ($r->status == 'menunggu' && in_array(session('role'), ['stoker', 'admin', 'owner'])): ?>
                                <a href="/retur/konfirmasi/<?= $r->id_retur ?>?status=disetujui" 
                                   class="btn btn-export-excel"
                                   onclick="return confirm('Setujui retur ini? Stok akan dikembalikan.')">
                                    Setujui
                                </a>
                                <a href="/retur/konfirmasi/<?= $r->id_retur ?>?status=ditolak" 
                                   class="btn btn-export-pdf"
                                   onclick="return confirm('Tolak retur ini?')">
                                    Tolak
                                </a>
                            <?php endif; ?>
                            <?php if (in_array(session('role'), ['stoker', 'admin', 'owner'])): ?>
                                <a href="/retur/hapus/<?= $r->id_retur ?>" 
                                   class="btn btn-export-pdf"
                                   onclick="return confirm('Hapus data retur ini?')">
                                    Hapus
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada pengajuan retur.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>