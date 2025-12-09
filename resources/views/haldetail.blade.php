@if(!empty($info))
    <p><strong>Pelaku Transaksi:</strong> {{ $info['nama_pelaku'] ?? '—' }}</p>
    <p><strong>Tanggal:</strong> {{ !empty($info['tanggal_transaksi']) ? date('d-m-Y', strtotime($info['tanggal_transaksi'])) : '-' }}</p>
    <p><strong>Total:</strong> Rp {{ number_format($info['total_harga'] ?? 0, 0, ',', '.') }}</p>
@endif

<table class="table table-bordered table-striped mt-3">
    <thead class="table-dark">
        <tr>
            <th>Barang</th>
            <th>Harga Satuan</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $d)
            <tr>
                <td>{{ $d->nama_barang }}</td>
                <td>Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                <td>{{ $d->jumlah }}</td>
                <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data detail transaksi.</td>
            </tr>
        @endforelse
    </tbody>
</table>
