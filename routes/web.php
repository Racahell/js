<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kontrol;

// Auth (publik)
Route::get('/login', [Kontrol::class, 'showlogin']);
Route::post('/login', [Kontrol::class, 'aksilogin']);
Route::get('/register', [Kontrol::class, 'showregister']);
Route::post('/register', [Kontrol::class, 'registerpost']);

// Logout (harus lewat POST)
Route::post('/logout', [Kontrol::class, 'logout']);

// Dashboard
Route::get('/dashboard', [Kontrol::class, 'dashboard']);

// Manajemen User
Route::get('/user', [Kontrol::class, 'user']);
Route::post('/resetpw/{id_user}', [Kontrol::class, 'resetpw']);
Route::get('/api/user', [Kontrol::class, 'apiUserList']); 

// Karyawan
Route::get('/halkaryawan', [Kontrol::class, 'halkaryawan']);
Route::post('/karyawan', [Kontrol::class, 'tambahkaryawan']);
Route::put('/karyawan/{id}', [Kontrol::class, 'updateKaryawan']);
Route::delete('/karyawan/hapus/{id_karyawan}', [Kontrol::class, 'hapusKaryawan']);
Route::get('/api/karyawan', [Kontrol::class, 'apiKaryawan']); // ✅ API untuk AJAX
// Tambahkan route API untuk data transaksi (menggantikan fungsi haltransaksi untuk tampilan tabel)

   
// Barang
Route::get('/halbarang', [Kontrol::class, 'halbarang']);
Route::get('/barang/tambah', [Kontrol::class, 'createBarang']);
Route::post('/barang', [Kontrol::class, 'storeBarang']);
Route::get('/barang/edit/{id}', [Kontrol::class, 'editBarang']);
Route::post('/barang/{id}', [Kontrol::class, 'updateBarang']);
Route::get('/barang/hapus/{id}', [Kontrol::class, 'hapusBarang']);

// Laporan
Route::get('/hallaporan', [Kontrol::class, 'laporan']);
Route::get('/owner-laporan', [Kontrol::class, 'laporanOwner']);

// Kasir
Route::get('/halkasir', [Kontrol::class, 'halkasir']);

// Transaksi
Route::get('/api/transaksi', [Kontrol::class, 'apiTransaksiList']); // List untuk tabel
Route::get('/api/transaksi/detail/{id}', [Kontrol::class, 'apiDetailTransaksi']); // Detail untuk modal
Route::get('/transaksi', [Kontrol::class, 'haltransaksi']); // Halaman container
Route::get('/transaksi/hapus/{id}', [Kontrol::class, 'hapustransaksi']);
Route::get('/transaksi/detail/{id}', [Kontrol::class, 'detailtransaksi']);
Route::get('/transaksi/detailp/{id}', [Kontrol::class, 'detailpembeli']);
Route::get('/daftartransaksi', [Kontrol::class, 'daftartransaksi']);
Route::get('/halriwayat', [Kontrol::class, 'riwayattransaksi']);

// Cetak & Export
Route::get('/printtransaksi/{id}', [Kontrol::class, 'cetakDetail']);
Route::get('/transaksi/export-pdf', [Kontrol::class, 'exportPdfTransaksi']);
Route::get('/transaksi/export-excel', [Kontrol::class, 'exportExcelTransaksi']);
Route::get('/keuangan/export-pdf', [Kontrol::class, 'exportPdfKeuangan']);
Route::get('/keuangan/export-excel', [Kontrol::class, 'exportExcelKeuangan']);

// Keranjang & Checkout
Route::get('/halpembeli', [Kontrol::class, 'viewpembeli']);
Route::post('/keranjang/tambah', [Kontrol::class, 'tambahKeranjang']);
Route::get('/keranjang', [Kontrol::class, 'lihatKeranjang']);
Route::delete('/keranjang/hapus/{id}', [Kontrol::class, 'hapusKeranjang']);
Route::post('/keranjang/proses', [Kontrol::class, 'prosesTransaksi']);
Route::get('/checkout', [Kontrol::class, 'showCheckout']);
Route::post('/checkout', [Kontrol::class, 'checkout']);
Route::post('/prosescheckout', [Kontrol::class, 'prosesCheckout']);
Route::get('/pembayaran/{id}', [Kontrol::class, 'halamanPembayaran']);

// Pengiriman
Route::get('/pengiriman', [Kontrol::class, 'halpengiriman']);
Route::get('/pengiriman/create/{id_detail}', [Kontrol::class, 'createPengiriman']);
Route::post('/pengiriman', [Kontrol::class, 'storePengiriman']);
Route::post('/pengiriman/kirim/{id_pengiriman}', [Kontrol::class, 'kirimPengiriman']);
Route::post('/pengiriman/terima/{id_pengiriman}', [Kontrol::class, 'terimaPengiriman']);
Route::get('/pengiriman/hapus/{id_pengiriman}', [Kontrol::class, 'hapusPengiriman']);

// Retur
Route::get('/retur', [Kontrol::class, 'halretur']);
Route::get('/retur/create/{id_detail}', [Kontrol::class, 'createRetur']);
Route::post('/retur', [Kontrol::class, 'storeRetur']);
Route::get('/retur/hapus/{id_retur}', [Kontrol::class, 'hapusRetur']);
Route::get('/retur/konfirmasi/{id_retur}', [Kontrol::class, 'konfirmasiRetur']);