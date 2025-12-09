<div class="container mt-4">
    <h2>Daftar Transaksi</h2>

    {{-- FORM FILTER/RESET - TETAP DENGAN SUBMIT STANDAR (BUKAN AJAX) --}}
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                <input type="date" 
                class="form-control" 
                id="tanggal_awal" 
                name="tanggal_awal" 
                value="{{ request('tanggal_awal') }}"
                max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                <input type="date" 
                class="form-control" 
                id="tanggal_akhir" 
                name="tanggal_akhir" 
                value="{{ request('tanggal_akhir') }}"
                max="{{ date('Y-m-d') }}"
                min="{{ request('tanggal_awal') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ url('/transaksi') }}"><button  class="btn btn-secondary w-100">Reset</button></a>
            </div>
        </div>
    </form>

    {{-- KELOMPOK TOMBOL CETAK/UNDUH - HARUS TETAP DI SINI, TAPI URL FILTER HARUS SESUAI --}}
    {{-- CATATAN: Visibilitas tombol ini akan diatur oleh JS tergantung apakah ada data atau tidak. --}}
    <div id="actionButtons" class="d-flex justify-content-start mb-3 gap-2" style="display: none;"> 
        @php
            // Ambil parameter filter dari URL
            $filterParams = request('tanggal_awal') || request('tanggal_akhir') ? 
                '?tanggal_awal=' . request('tanggal_awal') . '&tanggal_akhir=' . request('tanggal_akhir') : 
                '';
        @endphp
        
        <a href="/daftartransaksi{{ $filterParams }}" 
        target="_blank"
        class="btn btn-outline-secondary">
            Cetak Daftar Transaksi
        </a>
        <a href="/transaksi/export-pdf{{ $filterParams }}" 
        class="btn-export-pdf">Unduh PDF</a>
        
        <a href="/transaksi/export-excel{{ $filterParams }}" 
        class="btn btn-export-excel">Unduh Excel</a>
    </div>

    {{-- TEMPAT TABEL TRANSAKSI - TAMPILKAN SECARA DEFAULT (JS AKAN MENYEMBUNYIKAN JIKA KOSONG) --}}
    <div class="table-responsive">
        {{-- Alert No Data akan dimasukkan di sini oleh JS jika diperlukan --}}
        <table class="table table-striped" id="tabelTransaksi">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Pelaku</th>
                    <th>Total Harga</th>
                    <th>Jenis Barang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataTransaksiBody">
                {{-- Data transaksi akan diisi oleh JavaScript --}}
            </tbody>
        </table>
    </div>

    {{-- MODAL DETAIL TRANSAKSI - TETAP DI SINI --}}
    <div class="modal fade modal-fit-content" id="detailTransaksiModal" tabindex="-1" aria-labelledby="detailTransaksiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailTransaksiModalLabel">Detail Transaksi #<span id="modalTransaksiId"></span></h5>
                </div>
                <div class="modal-body">
                    <div id="modalDetailContent">
                        {{-- Konten detail akan diisi oleh JavaScript --}}
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    </div>
                    <div>
                        <a href="#" id="cetakTransaksiBtn" target="_blank" class="btn btn-info me-2">Cetak Transaksi</a>
                        <button type="button" id="hapusTransaksiBtn" class="btn btn-danger">Hapus Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    @media print {
        .btn, .alert, .table-dark a, form, #actionButtons { /* Tambahkan #actionButtons */
            display: none !important;
        }
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
<script src="{{ asset('js/haltransaksi.js') }}"></script>