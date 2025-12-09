<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard • Alat Musik</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />

</head>
<body>
<main>

@php
    $role = session('role');
@endphp

@if ($role === 'pembeli')
    <h2 class="fw-bold">Selamat Datang, {{ htmlspecialchars(session('nama_user')) }}</h2>
    <p class="text-muted">Temukan alat musik impian Anda di sini.</p>

    <a href="/keranjang" class="btn-soft" style="display: inline-block; margin: 16px 0;">Lihat Keranjang</a>

    <div class="card-soft">
        <div class="card-header-soft">Barang Tersedia</div>
        <div class="card-body">
            @if (!empty($barangTersedia))
                <div class="grid grid-cols-1" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                    @foreach ($barangTersedia as $b)
                        <div class="product-card">
                            <h6 class="fw-bold">{{ htmlspecialchars($b->nama_barang) }}</h6>
                            <p class="text-muted small">{{ htmlspecialchars($b->jenis) }}</p>
                            <p class="fw-bold">Rp {{ number_format($b->harga, 0, ',', '.') }}</p>
                            <p class="text-muted small">Stok: {{ $b->stok }}</p>
                            <form action="/keranjang/tambah" method="POST" style="margin-top: auto;">
                                @csrf
                                <input type="hidden" name="id_barang" value="{{ $b->id_barang }}">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="btn-soft">Tambah ke Keranjang</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted">Belum ada barang tersedia.</p>
            @endif
        </div>
    </div>

@elseif ($role === 'owner')
    <h2 class="fw-bold">Dashboard Owner</h2>

    <div class="grid grid-cols-4">
        <div class="stat-card">
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-value">{{ $totalKaryawan ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total User</div>
            <div class="stat-value">{{ $totalUser ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pendapatan</div>
            <div class="stat-value">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $totalTransaksi ?? 0 }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2" style="margin-top: 24px;">
        <div class="card-soft">
            <div class="card-header-soft">Manajemen Karyawan</div>
            <div class="btn-group">
                <a href="/halkaryawan" class="btn-soft">Kelola Karyawan</a>
                <a href="/user" class="btn-soft-outline">Kelola User</a>
            </div>
        </div>
        <div class="card-soft">
            <div class="card-header-soft">Laporan</div>
            <div class="btn-group">
                <a href="/owner-laporan" class="btn-soft">Lihat Laporan</a>
            </div>
        </div>
    </div>

@elseif ($role === 'admin')
    <h2 class="fw-bold">Dashboard Admin</h2>

    <div class="grid grid-cols-4">
        <div class="stat-card">
            <div class="stat-label">Total Barang</div>
            <div class="stat-value">{{ $totalBarang ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Stok Tersedia</div>
            <div class="stat-value">{{ $totalStok ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-value">{{ $transaksiHariIni ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pendapatan</div>
            <div class="stat-value">Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card-soft" style="margin-top: 24px;">
        <div class="card-header-soft">Transaksi & Laporan</div>
        <div class="btn-group">
            <a href="/transaksi" class="btn-soft">Lihat Transaksi</a>
            <a href="/hallaporan" class="btn-soft-outline">Laporan Keuangan</a>
        </div>
    </div>

@elseif ($role === 'stoker')
    <h2 class="fw-bold">Dashboard Stoker</h2>
    <p class="text-muted">Kelola persediaan barang dengan mudah.</p>

    <div class="grid grid-cols-3">
        <div class="stat-card">
            <div class="stat-label">Total Barang</div>
            <div class="stat-value">{{ $totalBarang ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Barang Hampir Habis</div>
            <div class="stat-value">{{ $barangHampirHabis ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Barang Masuk Hari Ini</div>
            <div class="stat-value">{{ $barangMasukHariIni ?? 0 }}</div>
        </div>
    </div>

    <div class="card-soft" style="margin-top: 24px;">
        <div class="card-header-soft">Manajemen Stok</div>
        <div class="btn-group">
            <a href="/halbarang" class="btn-soft">Kelola Barang</a>
            <a href="/supplier" class="btn-soft-outline">Daftar Supplier</a>
            <a href="/barangmasuk" class="btn-soft-outline">Barang Masuk</a>
        </div>
    </div>

@elseif ($role === 'kurir')
    <h2 class="fw-bold">Dashboard Kurir</h2>
    <p class="text-muted">Kelola dan selesaikan pengiriman barang.</p>

    <div class="grid grid-cols-3">
        <div class="stat-card">
            <div class="stat-label">Pengiriman Baru</div>
            <div class="stat-value">{{ $pengirimanBaru ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Dikirim Hari Ini</div>
            <div class="stat-value">{{ $dikirimHariIni ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Selesai</div>
            <div class="stat-value">{{ $pengirimanSelesai ?? 0 }}</div>
        </div>
    </div>

    <div class="card-soft" style="margin-top: 24px;">
        <div class="card-header-soft">Menu Pengiriman</div>
        <div class="btn-group">
            <a href="/pengiriman" class="btn-soft">Daftar Pengiriman</a>
            <a href="/pengiriman/riwayat" class="btn-soft-outline">Riwayat Pengiriman</a>
        </div>
    </div>

@else
    <div class="card-soft">
        <h4>Selamat Datang!</h4>
        <p>Silakan pilih menu di sidebar untuk memulai.</p>
    </div>
@endif

</main>

</body>
</html>