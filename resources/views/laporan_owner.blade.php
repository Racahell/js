
<div class="container mt-4">

    <h2 class="text-center mb-4">LAPORAN KEUANGAN PERUSAHAAN</h2>
    <h5 class="text-center mb-5">Toko Alat Musik Harmoni</h5>

    <!-- Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-4">

                    <h5>Total Karyawan</h5>
                    <h2><?= $totalKaryawan ?></h2>

        </div>
        <div class="col-md-4">

                    <h5>Total Pengguna</h5>
                    <h2><?= $totalUser ?></h2>

        </div>
        <div class="col-md-4">

                    <h5>Total Pendapatan</h5>
                    <h2>Rp <?= number_format($pendapatan, 0, ',', '.') ?></h2>

        </div>
    </div>

    <!-- Pendapatan Bulanan -->
    <h4 class="mt-5 mb-3">Pendapatan Bulanan</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>Bulan</th>
                    <th>Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporanBulanan as $l) { ?>
                <tr>
                    <td><?= $l['bulan'] ?></td>
                    <td class="text-end"><?= number_format($l['pendapatan'], 0, ',', '.') ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <!-- Tanda tangan -->
    <div class="signature">
        <p>Batam, <?= date('d F Y') ?></p>
        <p><strong><?= htmlspecialchars(session('nama_user')) ?></strong><br>Owner</p>
    </div>
