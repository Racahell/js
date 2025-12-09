<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Checkout Barang</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('/prosescheckout') }}" method="POST">
                @csrf

                @if($id_kasir)
                    <input type="hidden" name="id_karyawan" value="{{ $id_kasir }}">
                @else
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>Tidak ada kasir tersedia. Hubungi admin.</div>
                    </div>
                    <a href="{{ url('/keranjang') }}" class="btn btn-secondary">
                        Kembali ke Keranjang
                    </a>
                    @stop
                @endif

<br>
                <h2 class="mb-4">Daftar Barang di Keranjang:</h2>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Nama Barang</th>
                                <th scope="col" class="text-end">Harga</th>
                                <th scope="col" class="text-center">Jumlah</th>
                                <th scope="col" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @forelse($keranjang as $item)
                                @php
                                    $subtotal = $item['harga'] * $item['jumlah'];
                                    $total += $subtotal;
                                @endphp
                                <tr>
                                    <td>{{ $item['nama_barang'] }}</td>
                                    <td class="text-end">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item['jumlah'] }}</td>
                                    <td class="text-end">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Keranjang Anda kosong.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" style="text-align: center;">Total Keseluruhan:</td>
                                <td class="text-end" >Rp {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <br>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ url('/keranjang') }}" class="btn btn-secondary">
                         Kembali
                    </a>
                    <button type="submit" style="margin-left: 20px;" class="btn btn-success" {{ empty($keranjang) ? 'disabled' : '' }}>
                       Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>