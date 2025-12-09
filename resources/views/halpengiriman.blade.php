<div class="container mt-4">
    <h2>Manajemen Pengiriman</h2>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Transaksi</th>
                    <th>Barang</th>
                    <th>Pelanggan</th>
                    <th>Tanggal Kirim</th>
                    <th>Tanggal Terima</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengiriman as $p)
                <tr>
                    <td>{{ $p->id_pengiriman }}</td>
                    <td>{{ $p->id_transaksi }}</td>
                    <td>{{ $p->nama_barang }}</td>
                    <td>{{ $p->nama_pelanggan ?? '—' }}</td>
                    <td>
                        {{ $p->tanggal_kirim ? \Carbon\Carbon::parse($p->tanggal_kirim)->format('d-m-Y') : '-' }}
                    </td>
                    <td>
                        {{ $p->tanggal_menerima ? \Carbon\Carbon::parse($p->tanggal_menerima)->format('d-m-Y') : '-' }}
                    </td>
                    <td>
                        @if($p->tanggal_menerima)
                        <span class="badge bg-success">Diterima</span>
                        @elseif($p->tanggal_kirim)
                        <span class="badge bg-info">Dikirim</span>
                        @else
                        <span class="badge bg-warning">Menunggu Kirim</span>
                        @endif
                    </td>
                    <td>
                        @if(!$p->tanggal_kirim)
                        <form action="/pengiriman/kirim/{{ $p->id_pengiriman }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Kirim</button>
                        </form>
                        @elseif(!$p->tanggal_menerima)
                        <form action="/pengiriman/terima/{{ $p->id_pengiriman }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Konfirmasi Terima</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>