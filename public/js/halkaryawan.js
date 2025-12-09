// Ambil CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
let currentKaryawan = null;
let karyawanCache = {};
let currentPage = 1;
let lastPage = 1;

// ========== MODAL FUNCTIONS ==========
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    // Tambahkan kelas untuk overlay jika menggunakan modal-overlay
    const modal = document.getElementById(modalId);
    if (modal && modal.classList.contains('modal')) {
        // Asumsi modal-overlay digunakan jika tidak ada di HTML Anda
        // Jika Anda menggunakan CSS yang berbeda, sesuaikan di sini
    }
}
function closeModal(modalId) {
    if (modalId) {
        document.getElementById(modalId).style.display = 'none';
    } else {
        document.querySelectorAll('.modal, .modal-overlay').forEach(el => el.style.display = 'none');
    }
}
document.querySelectorAll('.modal').forEach(modal => {
    // Pastikan event listener untuk menutup modal berfungsi
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal.id);
    });
});

// ========== LOAD DATA ==========
function loadKaryawan(page = 1) {
    const tbody = document.getElementById('karyawanTableBody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>';
    }

    // ✅ PERBAIKAN: Menggunakan template literal yang benar (backtick)
    fetch(`/api/karyawan?page=${page}`)
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Respons bukan JSON — mungkin sesi login habis atau server error.');
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('karyawanTableBody');
            if (!tbody) return; // Keluar jika tabel tidak ada

            if (!data || !data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada data karyawan.</td></tr>';
                renderPagination(1, 1);
                return;
            }

            karyawanCache = {};
            data.data.forEach(k => {
                karyawanCache[k.id_karyawan] = k;
            });

            let rows = '';
            data.data.forEach((k, index) => {
                const no = (data.per_page * (page - 1)) + index + 1;
                // Mapping jabatan ID ke nama
                const jabatanNama = {
                    1: 'Owner', 2: 'Admin', 3: 'Stoker', 4: 'Kasir', 5: 'Kurir'
                }[k.id_jabatan] || '-';

                rows += `
                    <tr>
                        <td>${no}</td>
                        <td>${k.nama_karyawan || k.nama_user || '-'}</td>
                        <td>${k.alamat || '-'}</td>
                        <td>${jabatanNama}</td> <td>${k.notlp || '-'}</td>
                        <td>${k.email || '-'}</td>
                        <td>
                            <button type="button" class="btn-soft-outline" onclick="openDetailModal(${k.id_karyawan})">
                                Detail
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rows;
            currentPage = data.current_page;
            lastPage = data.last_page;
            renderPagination(currentPage, lastPage);
        })
        .catch(err => {
            console.error('Gagal memuat data:', err);
            const tbody = document.getElementById('karyawanTableBody');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data: ${err.message || 'Kesalahan jaringan'}</td></tr>`;
            }
            renderPagination(1, 1);
        });
}

function renderPagination(current, last) {
    const container = document.querySelector('.pagination-container');
    if (!container) return;
    if (last <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    // ✅ PERBAIKAN: Menggunakan backtick untuk template literal
    if (current > 1) {
        html += `<button class="pagination-btn" onclick="loadKaryawan(${current - 1})">‹ Sebelumnya</button>`;
    }
    if (current > 3) {
        html += `<button class="pagination-btn" onclick="loadKaryawan(1)">1</button>`;
        if (current > 4) html += `<span>...</span>`;
    }
    for (let i = Math.max(2, current - 2); i <= Math.min(last - 1, current + 2); i++) {
        html += `<button class="pagination-btn ${i === current ? 'active' : ''}" onclick="loadKaryawan(${i})">${i}</button>`;
    }
    if (current < last - 2) {
        if (current < last - 3) html += `<span>...</span>`;
        html += `<button class="pagination-btn" onclick="loadKaryawan(${last})">${last}</button>`;
    }
    if (current < last) {
        html += `<button class="pagination-btn" onclick="loadKaryawan(${current + 1})">Berikutnya ›</button>`;
    }
    container.innerHTML = html;
}

// ========== MODAL HANDLERS ==========
function openDetailModal(id) {
    const karyawan = karyawanCache[id];
    if (!karyawan) {
        alert('Data tidak ditemukan dalam cache! Coba refresh.');
        return;
    }
    currentKaryawan = karyawan;
    
    // Pastikan elemen input ada dan ID yang digunakan benar
    document.getElementById('modalId').value = karyawan.id_karyawan;
    document.getElementById('modalNama').value = karyawan.nama_karyawan || karyawan.nama_user || '';
    document.getElementById('modalAlamat').value = karyawan.alamat || '';
    
    // ✅ PERBAIKAN: Gunakan ID_JABATAN untuk set nilai di SELECT
    document.getElementById('modalJabatan').value = karyawan.id_jabatan || ''; 
    
    document.getElementById('modalTelepon').value = karyawan.notlp || '';
    document.getElementById('modalEmail').value = karyawan.email || '';
    
    showModal('detailModal');
}

function saveEditForm() {
    if (!currentKaryawan) {
        alert('Data karyawan tidak ditemukan!');
        return;
    }
    const id = currentKaryawan.id_karyawan;
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('nama_karyawan', document.getElementById('modalNama').value);
    formData.append('alamat', document.getElementById('modalAlamat').value);
    formData.append('id_jabatan', document.getElementById('modalJabatan').value);
    formData.append('notlp', document.getElementById('modalTelepon').value);
    formData.append('email', document.getElementById('modalEmail').value);

    // ✅ PERBAIKAN: Menggunakan template literal yang benar (backtick) untuk URL
    fetch(`/karyawan/${id}`, {
        method: 'POST', // Menggunakan POST untuk override PUT di Laravel/Framework
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || (data.status === 'success' ? 'Berhasil mengedit!' : 'Gagal mengedit!'));
        if (data.status === 'success') {
            closeModal('detailModal');
            loadKaryawan(currentPage);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi masalah koneksi saat mengedit.');
    });
}

function deleteKaryawan() {
    if (!currentKaryawan) {
        alert('Data karyawan tidak ditemukan!');
        return;
    }
    if (!confirm('Yakin hapus karyawan ini?')) return;
    
    const id = currentKaryawan.id_karyawan;
    
    // ✅ PERBAIKAN: Menggunakan template literal yang benar (backtick) untuk URL
    fetch(`/karyawan/hapus/${id}`, { 
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Berhasil dihapus!');
        if (data.status === 'success') {
            closeModal('detailModal');
            loadKaryawan(currentPage);
        }
    })
    .catch(err => {
        alert('Gagal menghapus! Periksa konsol.');
        console.error(err);
    });
}

function openAddModal() {
    document.getElementById('karyawanForm')?.reset();
    showModal('formModal');
}

// ========== TAMBAH KARYAWAN ==========
document.getElementById('karyawanForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/karyawan', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || (data.status === 'success' ? 'Berhasil menambahkan!' : 'Gagal menambahkan!'));
        if (data.status === 'success') {
            closeModal('formModal');
            loadKaryawan(1);
        }
    })
    .catch(err => {
        alert('Koneksi gagal saat menambah karyawan!');
        console.error(err);
    });
});

// ========== INIT ==========
document.addEventListener('DOMContentLoaded', () => {
    // Memastikan kita hanya memuat data karyawan jika elemen tabel ada (yaitu di halaman halkaryawan)
    if (document.getElementById('karyawanTableBody')) {
        loadKaryawan(1);
    }
    // Logic untuk login/register (jika di halaman terkait)
    // ... (Asumsi Anda punya logic terpisah di login.js dan register.js)
});