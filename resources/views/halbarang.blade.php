
    <div class="container mt-4">
        <h2>Daftar Barang</h2>
<br>
        <button class="btn" onclick="openModalTambahBarang()" style="margin-left: 30px;">Tambah Barang</button><br><br>

        <div class="table-responsive" >
            <table class="table table-bordered table-striped" border="1" cellpadding="8">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $b){ ?>
                        <tr>
                            <td><?= $b->kode_barang ?></td>
                            <td><?= $b->nama_barang ?></td>
                            <td><?= $b->jenis ?></td>
                            <td><?= $b->harga ?></td>
                            <td><?= $b->stok ?></td>
                            <td><?= $b->tanggal ?></td>
                            <td>
                                <button 
                                class="btn btn-danger btn-sm"
                                onclick="openModalBarang({
                                    id: '<?= $b->id_barang ?>',
                                    kode: '<?= $b->kode_barang ?>',
                                    nama: '<?= $b->nama_barang ?>',
                                    jenis: '<?= $b->jenis ?>',
                                    harga: '<?= $b->harga ?>',
                                    stok: '<?= $b->stok ?>',
                                    tanggal: '<?= $b->tanggal ?>'
                                })">
                                Detail
                            </button>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
    </div>

   
<div id="modalBarangg" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3 id="modalTitle">Tambah Barang</h3>
            <button class="btn-close" onclick="closeModalBarangg()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <form id="tambahbarang" method="POST" action="/barang">
                @csrf
                <input type="hidden" name="id_barang">

                <div class="mb-3">
                    <label class="form-label">Kode Barang:</label>
                    <input type="text" name="kode_barang" class="form-control" placeholder="Kode Barang" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Barang:</label>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis:</label>
                    <input type="text" name="jenis" class="form-control" placeholder="Jenis" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga:</label>
                    <input type="number" name="harga" class="form-control" placeholder="Harga" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok:</label>
                    <input type="number" name="stok" class="form-control" placeholder="Stok" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Masuk:</label>
                    <input type="date" name="tanggal" class="form-control"  required>
                </div>
            </form>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeModalBarangg()">Batal</button>
            <button type="submit" form="tambahbarang" class="btn">Simpan</button>
        </div>
    </div>
</div>

<!-- MODAL EDIT HTML -->
<div id="modalBarang" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3 id="modalTitle">Detail Barang</h3>
            <button class="btn-close" onclick="closeModalBarang()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <form id="editbarang" method="POST">
                @csrf
                <input type="hidden" name="id_barang" id="id_barang">

                <div class="mb-3">
                    <label class="form-label">Kode Barang:</label>
                    <input type="text" id="kodebarang" name="kode_barang" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Barang:</label>
                    <input type="text" id="namabarang" name="nama_barang" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis:</label>
                    <input type="text" id="jenis" name="jenis" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga:</label>
                    <input type="number" id="harga" name="harga" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok:</label>
                    <input type="number" id="stok" name="stok" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Masuk:</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                </div>
            </form>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeModalBarang()">Batal</button>
            <button type="button" class="btn-modal-delete" onclick="hapusBarang()">Hapus</button>
            <button type="submit" form="editbarang" class="btn">Simpan</button>
        </div>
    </div>
</div>


<script src="{{ asset('js/barang.js') }}"></script>