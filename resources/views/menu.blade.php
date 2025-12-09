<nav class="custom-navbar">
    <div class="navbar-container">
        <!-- Logo & Brand -->
        <div class="navbar-brand">
            <a href="/dashboard" class="brand-link">
                <img src="{{ asset('images/Asset 3.png') }}" alt="Logo" class="brand-logo">
                <span class="brand-text">Alat Musik</span>
            </a>
        </div>

        <!-- Hamburger Button (Mobile) -->
        <button class="navbar-toggler" aria-label="Toggle navigation" onclick="toggleNavbar()">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>

        <!-- Nav Menu -->
        <div class="navbar-menu" id="navbarMenu">
            <ul class="nav-list">
                <!-- PEMBELI -->
                @if (session('role') == 'pembeli')
                <li><a href="/halpembeli" class="nav-link {{ request()->is('halpembeli') ? 'active' : '' }}">Barang</a></li>
                <li><a href="/halriwayat" class="nav-link {{ request()->is('halriwayat') ? 'active' : '' }}">Riwayat Transaksi</a></li>
                @endif

                <!-- OWNER -->
                @if (session('role') == 'owner')
                <li>
                    <a href="/halkaryawan" class="nav-link {{ request()->is('halkaryawan') ? 'active' : '' }}">
                        Karyawan
                    </a>
                </li>
                <li><a href="/user" class="nav-link {{ request()->is('user') ? 'active' : '' }}">User</a></li>
                <li><a href="/owner-laporan" class="nav-link {{ request()->is('owner-laporan') ? 'active' : '' }}">Laporan</a></li>
                @endif

                <!-- ADMIN -->
                @if (session('role') == 'admin')
                <li><a href="/transaksi" class="nav-link {{ request()->is('transaksi') ? 'active' : '' }}">Transaksi</a></li>
                <li><a href="/hallaporan" class="nav-link {{ request()->is('hallaporan') ? 'active' : '' }}">Laporan Keuangan</a></li>
                @endif

                <!-- STOKER -->
                @if (session('role') == 'stoker')
                <li><a href="/halbarang" class="nav-link {{ request()->is('halbarang') ? 'active' : '' }}">Barang</a></li>
                <li><a href="/retur" class="nav-link {{ request()->is('retur') ? 'active' : '' }}">Retur</a></li>
                @endif

                <!-- KASIR -->
                @if (session('role') == 'kasir')
                <li><a href="/halkasir" class="nav-link {{ request()->is('halkasir') ? 'active' : '' }}">Kasir</a></li>
                @endif

                <!-- KURIR -->
                @if (session('role') == 'kurir')
                <li><a href="/pengiriman" class="nav-link {{ request()->is('pengiriman') ? 'active' : '' }}">Pengiriman</a></li>
                @endif
            </ul>

            <!-- Right Section: User Info & Logout -->
            <div class="navbar-right">
                @if (session('role') == 'pembeli')
                <a href="/keranjang" class="nav-cart">
                    Keranjang
                    <span class="cart-badge">
                        {{ array_sum(array_column(session('keranjang', []), 'jumlah')) }}
                    </span>
                </a>
                @endif

                <span class="user-greeting">Halo, {{ session('nama_user') ?? 'User' }}!</span>

                <form action="/logout" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script src="{{ asset('js/dashboard.js') }}"></script>
