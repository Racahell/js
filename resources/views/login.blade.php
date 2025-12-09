<div id="auth-section" style="{{ session('role') ? 'display: none;' : 'display: block;' }}">
    <div class="card-unique" id="loginCard">
        <div class="card-header-wave"><h3>Login</h3></div>
        <div class="card-body">
            <form id="loginForm">
                <div class="form-group">
                    <input type="text" id="username" class="form-input" placeholder=" ">
                    <label for="username" class="form-label">Username</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" class="form-input" placeholder=" ">
                    <label for="password" class="form-label">Password</label>
                </div>
                <button type="submit" class="btn-shine">Login</button>
                <a class="text-link" id="showRegister">Belum punya akun? Daftar</a>
            </form>
        </div>
    </div>

    <div class="card-unique" id="registerCard" style="display: none;">
        <div class="card-header-wave"><h3>Register</h3></div>
        <div class="card-body">
            <form id="registerForm">
                <div class="form-group">
                    <input type="text" id="nama_user" name="nama_user" class="form-input" placeholder=" " required>
                    <label for="nama_user" class="form-label">Nama User</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-input" placeholder=" " required>
                    <label for="password" class="form-label">Password</label>
                </div>
                <div class="form-group">
                    <input type="text" id="alamat" name="alamat" class="form-input" placeholder=" " required>
                    <label for="alamat" class="form-label">Alamat</label>
                </div>
                <div class="form-group">
                    <input type="number" id="notlp" name="notlp" class="form-input" placeholder=" " required>
                    <label for="notlp" class="form-label">Telepon</label>
                </div>
                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-input" placeholder=" " required>
                    <label for="email" class="form-label">Email</label>
                </div>
                <button type="submit" class="btn-register">Daftar</button>
                <a class="text-link" id="showLogin">Sudah punya akun? Login</a>
            </form>
        </div>
    </div>
</div>

{{-- =============== BAGIAN DAFTAR KARYAWAN (Tampil hanya jika URL adalah /halkaryawan) =============== --}}
<div id="karyawan-section" style="{{ request()->is('halkaryawan') && session('id') ? 'display: block;' : 'display: none;' }}">
    <div class="karyawan-container">
        <div class="karyawan-card">
            <h2>Daftar Karyawan</h2>
            <div class="karyawan-header">
                <button type="button" class="btn-karyawan-primary" onclick="openAddModal()">
                    Tambah Karyawan
                </button>
                <a href="/dashboard" class="btn-karyawan-outline">Kembali ke Dashboard</a>
            </div>
            <div class="table-responsive">
                <table class="table-karyawan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Alamat</th>
                            <th>Jabatan</th>
                            <th>Nomor Telepon</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="karyawanTableBody">
                        <tr><td colspan="7" class="text-center">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center; gap: 8px;"></div>
        </div>
    </div>
</div>

<div class="modal" id="formModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan</h5>
            </div>
            <form id="karyawanForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama User / Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_user" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password Akun <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 3 karakter" required>
                    </div>

                    <div class="mb-3">
                        <label>Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Jabatan <span class="text-danger">*</span></label>
                        <select name="jabatan" class="form-control" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="1">Owner</option>
                            <option value="2">Admin</option>
                            <option value="3">Stoker</option>
                            <option value="4">Kasir</option>
                            <option value="5">Kurir</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="notlp" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-soft-outline" onclick="closeModal('formModal')">Batal</button>
                    <button type="submit" class="btn-soft">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="detailModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Karyawan</h5>
                <button type="button" class="btn-close" onclick="closeModal('detailModal')">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalId">
                <div class="mb-3">
                    <label>Nama Karyawan</label>
                    <input type="text" id="modalNama" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <textarea id="modalAlamat" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Jabatan</label>
                    <select id="modalJabatan" class="form-control">
                        <option value="1">Owner</option>
                        <option value="2">Admin</option>
                        <option value="3">Stoker</option>
                        <option value="4">Kasir</option>
                        <option value="5">Kurir</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Telepon</label>
                    <input type="text" id="modalTelepon" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" id="modalEmail" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-soft-outline" onclick="closeModal('detailModal')">Batal</button>
                <button type="button" class="btn-soft" onclick="saveEditForm()">Edit</button>
                <button type="button" class="btn-soft-outline" style="background:#dc3545;color:white;" onclick="deleteKaryawan()">Hapus</button>
            </div>
        </div>
    </div>
</div>

{{-- =============== BAGIAN DAFTAR USER (Tampil hanya jika URL adalah /user) =============== --}}
<div class="user-page-container" id="userListSection" style="{{ request()->is('user') && session('id') ? 'display: block;' : 'display: none;' }}">
    <div class="user-card" id="userListCard">
        <h2>Daftar User</h2>

        <div class="mb-3">
            <a href="/dashboard" class="btn-outline-secondary">Kembali ke Dashboard</a>
        </div>

        <div class="table-responsive">
            <table class="table-user table align-middle" border="1" cellpadding="5" cellspacing="0" id="userTable">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr><td colspan="3" class="text-center">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI RESET PASSWORD --}}
<div id="confirmResetModal" class="modal-overlay" style="display: none;">
    <div class="modal-box mx-auto">
        <h4 id="confirmResetMessage">Yakin ingin mereset password user ini menjadi 12345?</h4>
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            <button onclick="closeModal('confirmResetModal')" class="modal-btn" style="background: #ccc; color: #333;">Batal</button>
            <button id="executeResetBtn" class="modal-btn" style="background: #dc3545;">Ya, Reset</button>
        </div>
    </div>
</div>

{{-- MODAL POPUP UMUM --}}
<div id="popupModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h4 id="modalMessage"></h4>
        <button onclick="closeModal('popupModal')" class="modal-btn">OK</button>
    </div>
</div>

<script src="{{ asset('js/login.js') }}"></script>
<script src="{{ asset('js/register.js') }}"></script> 
<script src="{{ asset('js/halkaryawan.js') }}"></script> 
<script src="{{ asset('js/user_management.js') }}"></script>