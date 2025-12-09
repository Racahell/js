function showModal(message) {
    document.getElementById("modalMessage").innerHTML = message;
    document.getElementById("popupModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("popupModal").style.display = "none";
}

document.getElementById('registerForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    try {
        const res = await fetch('/register', {
            method: 'POST',
            body: formData // Kirim sebagai form
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch {
            window.location.reload();
            return;
        }

        if (data.status === 'success') {
            showModal('Registrasi berhasil! Mengalihkan ke login...');
            setTimeout(() => {
                document.getElementById('registerCard').style.display = 'none';
                document.getElementById('loginCard').style.display = 'block';
                // Opsional: reset form
                document.getElementById('registerForm').reset();
            }, 1500);
        } else {
            // Tampilkan error validasi Laravel
            if (data.errors) {
                let msg = '';
                for (let field in data.errors) {
                    msg += data.errors[field][0] + '<br>';
                }
                showModal(msg);
            } else {
                showModal(data.message || 'Registrasi gagal!');
            }
        }
    } catch (err) {
        console.error(err);
        showModal('Koneksi gagal!');
    }
});
document.getElementById("showRegister").onclick = () => {
    document.getElementById("loginCard").style.display = "none";
    document.getElementById("registerCard").style.display = "block";
};

document.getElementById("showLogin").onclick = () => {
    document.getElementById("registerCard").style.display = "none";
    document.getElementById("loginCard").style.display = "block";
};