function openModalBarang(data = null) {
    document.getElementById('id_barang').value = data.id;
    document.getElementById('kodebarang').value = data.kode;
    document.getElementById('namabarang').value = data.nama;
    document.getElementById('jenis').value = data.jenis;
    document.getElementById('harga').value = data.harga;
    document.getElementById('stok').value = data.stok;
    document.getElementById('tanggal').value = data.tanggal;

    document.getElementById('modalBarang').classList.add('show');

        // 🔥 SET ACTION FORM SAAT EDIT
        document.getElementById("editbarang").action = "/barang/" + data.id;
    

}

// Tutup modal
function closeModalBarang() {
    document.getElementById('modalBarang').style.display = 'none';
}

// Tutup jika klik di luar box modal
window.onclick = function(e) {
    const modal = document.getElementById('modalBarang');
    if (e.target === modal) {
        modal.style.display = "none";
    }
};

function hapusBarang() {
    let id = document.getElementById("id_barang").value;

    if (!confirm("Hapus barang ini?")) return;

    fetch("/barang/hapus/" + id, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(res => location.reload());
}


function openModalTambahBarang() {
    document.getElementById('modalBarangg').classList.add('show');
}

function closeModalBarangg() {
    document.getElementById('modalBarangg').classList.remove('show');
}

window.onclick = function(e) {
    const modal = document.getElementById('modalBarangg');
    if (e.target === modal) {
        modal.style.display = "none";
    }
};
