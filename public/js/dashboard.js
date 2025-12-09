
    // ========== MODAL ==========
    function showModal(message) {
        document.getElementById("modalMessage").innerHTML = message;
        document.getElementById("popupModal").style.display = "flex";
    }
    function closeModal() {
        document.getElementById("popupModal").style.display = "none";
    }

    // ========== TAMBAH KE KERANJANG ==========
    window.addToCart = async function(e, idBarang) {
        e.preventDefault();
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const res = await fetch('/keranjang/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ id_barang: idBarang, jumlah: 1 })
            });
            const result = await res.json();
            if (result.success) {
                showModal('Barang ditambahkan ke keranjang!');
            } else {
                showModal(result.message || 'Gagal menambahkan!');
            }
        } catch (err) {
            showModal('Koneksi gagal!');
        }
    };

    // ========== LOGOUT ==========
    window.logout = async function() {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/logout', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf }
        });
        window.location.reload();
    };

    // ========== TOGGLE NAVBAR ==========
    window.toggleNavbar = function() {
        document.getElementById('navbarMenu')?.classList.toggle('active');
    };

    // ========== SHOW SECTION (pembeli) ==========
    window.showSection = function(section) {
        document.querySelectorAll('[id^="section-"]').forEach(el => el.style.display = 'none');
        const target = document.getElementById('section-' + section);
        if (target) target.style.display = 'block';
    };
