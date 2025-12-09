<div class="container mt-4">
    <h2>Keranjang Belanja Anda</h2>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($keranjang as $index => $item){ ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td><?= $item['jumlah'] ?></td>
                                <td>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-success">
                            <th colspan="3">TOTAL</th>
                            <th>Rp <?php 
                                $total = 0;
                                foreach($keranjang as $item) {
                                    $total += $item['harga'] * $item['jumlah'];
                                }
                                echo number_format($total, 0, ',', '.');
                                ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <form action="/checkout" method="POST">
                @csrf
                <button type="submit" class="btn btn-success btn-lg w-100">
                    Lanjutkan ke Pembayaran
                </button>
            </form>

        <a href="/halpembeli" class="btn btn-outline-primary mt-3">
        Kembali ke Belanja
        </a>
    </div>