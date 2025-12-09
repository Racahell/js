document.addEventListener('DOMContentLoaded', function() {
    // URL API untuk mengambil data transaksi. 
    const apiUrl = '/api/transaksi' + window.location.search; 
    
    const detailTransaksiModal = document.getElementById('detailTransaksiModal');
    
    // --- Fungsi Utilitas ---
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        }).format(amount).replace('Rp', 'Rp ');
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        }).replace(/\//g, '-');
    }
    
    function showModal(modalElement) {
        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-hidden', 'false');
        modalElement.classList.add('show');
        document.body.classList.add('modal-open'); 
    }

    function hideModal(modalElement) {
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    // 🛑 FUNGSI TEMPLATING BARU (Menggantikan View PHP) 🛑
    function renderDetailContent(data) {
        const info = data.info;
        const items = data.items;
        let html = '';

        if (!info || items.length === 0) {
            // Konten saat tidak ada data
            return '<div class="alert alert-warning mt-3">Tidak ada data detail transaksi.</div>';
        }

        // 1. Render Info Utama (Sesuaikan dengan format yang Anda inginkan)
        html += `
            <p><strong>Pelaku Transaksi:</strong> ${info.nama_pelaku}</p>
            <p><strong>Tanggal:</strong> ${formatDate(info.tanggal_transaksi)}</p>
            <p><strong>Total:</strong> ${formatCurrency(info.total_harga)}</p>

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
        `;

        // 2. Render Detail Barang
items.forEach(item => {
    html += `
        <tr>
            <td>${item.nama_barang}</td>
            <td>${formatCurrency(item.harga)}</td>
            <td>${item.jumlah}</td> 
            <td>${formatCurrency(item.subtotal)}</td>
        </tr>
    `;
});



        // 3. Tutup tag
        html += `
                </tbody>
            </table>
        `;
        return html;
    }

    // --- Logika Utama ---

    function loadTransaksiData(url) {
        const tbody = document.getElementById('dataTransaksiBody');
        const tableWrapper = document.querySelector('#tabelTransaksi') ? document.querySelector('#tabelTransaksi').closest('.table-responsive') : null;
        const actionButtons = document.getElementById('actionButtons');
        
        if (!tableWrapper || !actionButtons) {
            console.error('Elemen tabel atau tombol aksi tidak ditemukan. Periksa HTML.');
            return;
        }
        
        const existingAlert = tableWrapper.parentNode.querySelector('.alert-info') || tableWrapper.parentNode.querySelector('.alert-danger');
        if (existingAlert) {
            existingAlert.remove();
        }

        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Memuat data...</td></tr>';

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data: HTTP Status ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                const transaksi = data.data; 
                tbody.innerHTML = ''; 

                if (transaksi.length === 0) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-info';
                    alertDiv.textContent = 'Tidak ada transaksi ditemukan.';
                    tableWrapper.parentNode.insertBefore(alertDiv, tableWrapper);
                    
                    tableWrapper.style.display = 'none';
                    actionButtons.style.display = 'none';
                    return;
                }
                
                tableWrapper.style.display = 'block';
                actionButtons.style.display = 'flex';

                transaksi.forEach(t => {
                    const totalHargaFormatted = formatCurrency(t.total_harga);
                    const tanggalFormatted = formatDate(t.tanggal_transaksi);
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${t.id_transaksi}</td>
                        <td>${tanggalFormatted}</td>
                        <td>${t.nama_pelaku}</td>
                        <td>${totalHargaFormatted}</td>
                        <td>${t.jumlah_jenis_barang}</td>
                        <td class="text-center">
                        <button type="button"
                        class="btn btn-sm btn-info btn-transaksi-detail-js"
                        data-id="${t.id_transaksi}">
                        Detail
                        </button>

                        </td>
                        `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Fetch Error Data Transaksi:', error);
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.textContent = `Gagal memuat data transaksi. (${error.message})`;
                
                tableWrapper.parentNode.insertBefore(alertDiv, tableWrapper);
                tbody.innerHTML = ''; 
            });
    }

    loadTransaksiData(apiUrl);
    
    // 2. Event delegation untuk tombol Detail & Modal
    document.addEventListener('click', function(event) {
        const target = event.target;

        // A. Trigger Detail Button (.btn-transaksi-detail-js)
        if (target.classList.contains('btn-transaksi-detail-js')) {
            const idTransaksi = target.dataset.id;
            const detailUrl = '/api/transaksi/detail/' + idTransaksi; 
            
            const modalTransaksiId = document.getElementById('modalTransaksiId');
            const cetakTransaksiBtn = document.getElementById('cetakTransaksiBtn');
            const hapusTransaksiBtn = document.getElementById('hapusTransaksiBtn');
            const modalDetailContent = document.getElementById('modalDetailContent');

            modalTransaksiId.textContent = idTransaksi;
            cetakTransaksiBtn.href = `/printtransaksi/${idTransaksi}`; 
            
            hapusTransaksiBtn.onclick = function() {
                if (confirm('Yakin hapus transaksi #' + idTransaksi + '? Tindakan ini tidak dapat dibatalkan.')) {
                    window.location.href = `/transaksi/hapus/${idTransaksi}`; 
                }
            };

            modalDetailContent.innerHTML = '<p class="text-center">Memuat detail...</p>';
            fetch(detailUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Gagal memuat detail (HTTP Status: ${response.status}).`);
                    }
                    return response.json();
                })
                .then(data => {
                    modalDetailContent.innerHTML = renderDetailContent(data); 
                })
                .catch(error => {
                    console.error('Detail Fetch Error:', error);
                    modalDetailContent.innerHTML = `<div class="alert alert-danger">Gagal memuat detail transaksi. (${error.message})</div>`;
                });
            
            showModal(detailTransaksiModal);
        }

        // B. Trigger Close Modal Button 
        if (target.dataset.bsDismiss === 'modal' || target.classList.contains('btn-close')) {
            if (target.closest('#detailTransaksiModal') === detailTransaksiModal) {
                 hideModal(detailTransaksiModal);
            }
        }

        // C. Tutup modal ketika klik di luar (backdrop)
        if (detailTransaksiModal) {
            detailTransaksiModal.addEventListener('click', function(event) {
                if (event.target === detailTransaksiModal) {
                    hideModal(detailTransaksiModal);
                }
            });
        }
    });
});
