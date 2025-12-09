function openModalDetail(idTransaksi) {

    // Buka modal
    document.getElementById("modalDetail").style.display = "flex";

    // Ambil area konten modal
    let modalContent = document.getElementById("modalDetailContent");

    // Tampilkan loading
    modalContent.innerHTML = "<p>Loading...</p>";

    // AJAX ambil data transaksi detail
    fetch(`/api/transaksi/detail/${idTransaksi}`)
        .then(res => res.json())
        .then(res => {

            let info = res.info;
            let data = res.items;

            if (!data || data.length === 0) {
                modalContent.innerHTML = "<div class='alert-warning'>Transaksi tidak ditemukan.</div>";
                return;
            }

            // HEADER MODAL
            let html = `
                <span class="close" onclick="closeModalDetail()">&times;</span>
                <h2>Detail Transaksi</h2>

                <p><strong>Pelaku:</strong> ${info.nama_pelaku}</p>
                <p><strong>Tanggal:</strong> ${info.tanggal_transaksi}</p>
                <p><strong>Total:</strong> Rp ${Number(info.total_harga).toLocaleString()}</p>

                <div class="table-container">
                    <table border="1" cellpadding="10">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            // LIST ITEM
            data.forEach(d => {
                let subtotal = d.harga * d.jumlah;

                html += `
                    <tr>
                        <td>${d.nama_barang}</td>
                        <td>Rp ${Number(d.harga).toLocaleString()}</td>
                        <td>${d.jumlah}</td>
                        <td>Rp ${subtotal.toLocaleString()}</td>
                        <td>
                            ${d.retur == 1 
                                ? '<span class="badge-info">Retur Diajukan</span>'
                                : `<a href="/retur/create/${d.id_detail}" class="btn-primary">Ajukan Retur</a>`
                            }
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            // Tampilkan ke modal
            modalContent.innerHTML = html;
        })
        .catch(err => {
            modalContent.innerHTML = "<div class='alert-warning'>Gagal memuat data.</div>";
        });
}


function closeModalDetail() {
    document.getElementById("modalDetail").style.display = "none";
}
