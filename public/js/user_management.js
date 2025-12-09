// public/js/user_management.js

document.addEventListener('DOMContentLoaded', function () {
    // Ambil CSRF Token dari meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Variabel untuk menyimpan ID user yang akan direset
    let userIdToReset = null; 

    // ---------------------------------------------
    // --- MODAL FUNCTIONS (ASUMSI GLOBAL) ---
    // Diulang di sini sebagai fallback/standarisasi
    
    if (typeof window.showModal !== 'function') {
        window.showModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'flex';
        };
    }

    if (typeof window.closeModal !== 'function') {
        window.closeModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        };
    }

    // Fungsi untuk menampilkan pesan pop-up umum
    window.showPopupMessage = function(message) {
        const popup = document.getElementById('popupModal');
        if (popup) {
             document.getElementById('modalMessage').textContent = message;
             showModal('popupModal');
        } else {
             alert(message);
        }
    }
    // ---------------------------------------------


    // --- FUNGSI LOAD USER ---
    window.loadUser = function() {
        const userSection = document.getElementById('userListSection');
        const tbody = document.getElementById('userTableBody');
        
        // HANYA jalankan jika User Section terlihat (sesuai Blade logic)
        if (!tbody || !userSection || userSection.style.display === 'none') {
             return; 
        }
        
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Memuat data...</td></tr>';
        
        // Memanggil endpoint API untuk daftar user
        fetch(`/api/user`) 
        .then(response => {
            if (!response.ok) {
                // Mencoba membaca pesan error dari JSON response
                return response.json().then(err => {
                    throw new Error(err.message || `Gagal memuat. Status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center">Tidak ada data user.</td></tr>`;
                return;
            }

            let rows = '';
            data.forEach((user, index) => {
                const no = index + 1;
                // Escape nama user untuk menghindari error JS di onclick
                const escapedName = user.nama_user ? user.nama_user.replace(/'/g, "\\'") : 'User';
                
                rows += `
                <tr>
                    <td>${no}</td>
                    <td>${user.nama_user || '-'}</td>
                    <td>
                        <button type="button" class="btn-reset-password" 
                            onclick="promptResetPassword(${user.id_user}, '${escapedName}')">
                            Reset Password
                        </button>
                    </td>
                </tr>
                `;
            });
            tbody.innerHTML = rows;
        })
        .catch(err => {
            tbody.innerHTML = `<tr>
            <td colspan="3" class="text-center text-danger">Gagal memuat data: ${err.message}</td></tr>`;
            console.error('Error load user:', err);
        });
    };

    // --- FUNGSI TRIGGER MODAL KONFIRMASI ---
    window.promptResetPassword = function(userId, userName) {
        if (!csrfToken) {
            return showPopupMessage('Aksi gagal: CSRF Token tidak tersedia.');
        }
        userIdToReset = userId;
        document.getElementById('confirmResetMessage').innerHTML = `Yakin ingin mereset password user **${userName}** menjadi **12345**?`;
        showModal('confirmResetModal');
    };


    // --- FUNGSI EXECUTE RESET PASSWORD (AJAX) ---
    const executeResetBtn = document.getElementById('executeResetBtn');
    if(executeResetBtn) {
        executeResetBtn.addEventListener('click', function() {
            if (!userIdToReset) return;
            if (!csrfToken) return showPopupMessage('Aksi gagal: CSRF Token tidak tersedia.');
            
            closeModal('confirmResetModal');

            // Lakukan permintaan POST ke Controller
            fetch(`/resetpw/${userIdToReset}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || `Gagal mereset. Status: ${response.status}`);
                    });
                }
                return response.json(); 
            })
            .then(data => {
                showPopupMessage(data.message);
                userIdToReset = null; 
                if (data.status === 'success') {
                    loadUser(); // Muat ulang tabel user setelah reset sukses
                }
            })
            .catch(error => {
                console.error('Error saat melakukan reset password:', error);
                showPopupMessage(`Terjadi kesalahan: ${error.message || 'Gagal terhubung ke server.'}`);
                userIdToReset = null; 
            });
        });
    }

    // Init: Panggil fungsi untuk memuat data user jika elemennya terlihat saat DOM dimuat
    if (document.getElementById('userListSection')?.style.display !== 'none') {
        window.loadUser();
    }
});