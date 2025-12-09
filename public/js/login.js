// ------------------------------
// FUNGSI MODAL
// ------------------------------
function showModal(message) {
    document.getElementById("modalMessage").innerHTML = message; // Gunakan innerHTML agar link bisa diklik
    document.getElementById("popupModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("popupModal").style.display = "none";
}

// ------------------------------
// TOGGLE LOGIN / REGISTER
// ------------------------------
document.getElementById('showRegister')?.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('loginCard').style.display = 'none';
    document.getElementById('registerCard').style.display = 'block';
});

document.getElementById('showLogin')?.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('registerCard').style.display = 'none';
    document.getElementById('loginCard').style.display = 'block';
});

// ------------------------------
// TOGGLE NAVBAR (MOBILE)
// ------------------------------
function toggleNavbar() {
    document.getElementById('navbarMenu')?.classList.toggle('active');
}

// ------------------------------
// LOGIN DENGAN AJAX
// ------------------------------
document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        showModal("Silakan isi username dan password!");
        return;
    }

    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    try {
        const res = await fetch('/login', {
            method: 'POST',
            body: formData // Kirim sebagai form, bukan JSON
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch {
            // Jika bukan JSON, kemungkinan redirect — reload saja
            window.location.reload();
            return;
        }

        if (data.status === 'success') {
            window.location.href = data.redirect || '/dashboard';
        } else {
            showModal((data.message || 'Username atau password salah!') + 
                '<br><br><a href="#" id="showRegisterLink">Daftar Akun</a>');
            // Re-bind event untuk link dataftar
            document.getElementById('showRegisterLink')?.addEventListener('click', e => {
                e.preventDefault();
                document.getElementById('loginCard').style.display = 'none';
                document.getElementById('registerCard').style.display = 'block';
            });
        }
    } catch (err) {
        console.error(err);
        showModal('Koneksi gagal!');
    }
});
// ------------------------------
// REGISTER (opsional)
// ------------------------------
document.getElementById('registerForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    alert('Fitur register via AJAX bisa ditambahkan di sini.');
});