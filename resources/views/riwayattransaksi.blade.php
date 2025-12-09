<div class="container mt-4">
        <h2>Daftar Barang</h2>
<div class="card-body">
                <?php if (!empty($transaksiTerakhir)) { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksiTerakhir as $t) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($t->id_transaksi) ?></td>
                                        <td><?= htmlspecialchars($t->tanggal_transaksi) ?></td>
                                        <td>Rp <?= number_format($t->total_harga, 0, ',', '.') ?></td>
                                        <td>
                                            <?php 
                                            $status = $t->status_pengiriman;
                                            if ($status == 'Diterima') {
                                                $badgeClass = 'bg-success';
                                            } elseif ($status == 'Dikirim') {
                                                $badgeClass = 'bg-info';
                                            } else {
                                                $badgeClass = 'bg-warning';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                        </td>
                                        <?php if (session('level')==2) {
                                        ?>
                                        <td>
                                            <button onclick="openModalDetail(<?= $t->id_transaksi ?>)" class="btn btn-sm btn-primary">Detail</button>

                                        </td>
                                    <?php }?>
                                    <?php if (session('level')==1) {
                                        ?>
                                        <td>
                                            <a href="/transaksi/detail/<?= $t->id_transaksi ?>" class="btn btn-sm btn-primary">Detail</a>
                                        </td>
                                    <?php }?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-center text-muted">Belum ada transaksi.</p>
                <?php } ?>
            </div>
        </div>

<!-- ====================== MODAL BACKGROUND ====================== -->
<div id="modalDetail" class="modal">
    <div class="modal-content" id="modalDetailContent">
        <!-- JS akan mengisi bagian ini -->
    </div>
</div>


<script src="{{asset ('js/halriwayat.js')}}"></script>