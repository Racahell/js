<div class="container mt-4">
    <h2>Buat Pengiriman</h2>
    <div class="alert alert-info">
        <p><strong>Transaksi:</strong> {{ $detail->id_transaksi }}</p>
        <p><strong>Barang:</strong> {{ $detail->nama_barang }}</p>
        <p><strong>Jumlah:</strong> {{ $detail->jumlah }}</p>
    </div>

    <form action="/pengiriman" method="POST">
        @csrf
        <input type="hidden" name="id_detail" value="{{ $detail->id_detail }}">
        <button type="submit" class="btn btn-primary">Buat Pengiriman</button>
        <a href="/pengiriman" class="btn btn-secondary">Batal</a>
    </form>
</div>