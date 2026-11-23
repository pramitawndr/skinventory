@php
    $pageInfo = match (true) {
        request()->routeIs('dashboard') => [
            'Dashboard',
            'Dashboard',
            'Sistem manajemen inventaris produk skincare.'
        ],

        request()->routeIs('products.*') => [
            'Data Produk',
            'Data Produk',
            'Kelola informasi dan data produk skincare.'
        ],

        request()->routeIs('batches.*') => [
            'Data Batch',
            'Data Batch',
            'Kelola batch produksi dan informasi kedaluwarsa.'
        ],

        request()->routeIs('stock-reports.*') => [
            'Transaksi Stok',
            'Transaksi Stok',
            'Kelola pengajuan dan pantau pencatatan stok produk.'
        ],

        request()->routeIs('stock-transactions.*') => [
            'Riwayat Transaksi',
            'Riwayat Transaksi',
            'Lihat seluruh transaksi stok yang telah dicatat.'
        ],

        default => [
            'SKINVENTORY',
            'SKINVENTORY',
            'Inventory Management System.'
        ],
    };
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SKINVENTORY') | SKINVENTORY</title>

    <link rel="stylesheet" href="{{ asset('css/skinventory.css') }}">
</head>

<body>
    <div class="app-shell">

        {{-- SIDEBAR --}}
        <aside class="sidebar">

            {{-- BRAND --}}
            <div class="brand">
                <div class="brand-mark">S</div>

                <div>
                    <div class="brand-name">
                        SKIN<span>VENTORY</span>
                    </div>

                    <div class="brand-caption">
                        INVENTORY MANAGEMENT
                    </div>
                </div>
            </div>

            {{-- MAIN MENU --}}
            <div class="sidebar-section">MAIN MENU</div>

            <nav class="sidebar-nav">

                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span>
                    Dashboard
                </a>

                <a href="{{ route('products.index') }}"
                   class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span class="nav-icon">▤</span>
                    Data Produk
                </a>

                <a href="{{ route('batches.index') }}"
                   class="{{ request()->routeIs('batches.*') ? 'active' : '' }}">
                    <span class="nav-icon">▣</span>
                    Data Batch
                </a>

                <a href="{{ route('stock-reports.index') }}"
                   class="{{ request()->routeIs('stock-reports.*') ? 'active' : '' }}">
                    <span class="nav-icon">⇄</span>
                    Transaksi Stok
                </a>

                {{-- Pelacakan Batch sementara belum aktif --}}
                <span class="nav-disabled">
                    <span class="nav-icon">⌕</span>
                    Pelacakan Batch
                </span>

            </nav>

            {{-- BLOCKCHAIN --}}
            <div class="sidebar-section">BLOCKCHAIN</div>

            <nav class="sidebar-nav">

                {{-- Verifikasi Blockchain sementara belum aktif --}}
                <span class="nav-disabled">
                    <span class="nav-icon">◇</span>
                    Verifikasi Blockchain
                </span>

                <a href="{{ route('stock-transactions.index') }}"
                   class="{{ request()->routeIs('stock-transactions.*') ? 'active' : '' }}">
                    <span class="nav-icon">▧</span>
                    Riwayat Transaksi
                </a>

            </nav>

            {{-- MANAGEMENT --}}
            <div class="sidebar-section">MANAGEMENT</div>

            <nav class="sidebar-nav">

                <span class="nav-disabled">
                    <span class="nav-icon">▥</span>
                    Laporan
                </span>

                @if(auth()->user()->role === 'auditor')
                    <span class="nav-disabled">
                        <span class="nav-icon">♙</span>
                        Manajemen Pengguna
                    </span>
                @endif

            </nav>

            {{-- USER INFORMATION --}}
            <div class="sidebar-bottom">

                <div class="user-info">

                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="user-details">
                        <strong>{{ auth()->user()->name }}</strong>

                        <small>
                            {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                        </small>
                    </div>

                </div>

                {{-- LOGOUT --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button class="logout-button" type="submit">
                        ↪ Keluar
                    </button>
                </form>

            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main-content">

            {{-- TOPBAR --}}
            <header class="topbar">

                <div>
                    <div class="breadcrumb">
                        SKINVENTORY / {{ $pageInfo[0] }}
                    </div>

                    <h1>{{ $pageInfo[1] }}</h1>

                    <p>{{ $pageInfo[2] }}</p>
                </div>

                <div class="topbar-right">

                    <div class="network-status">
                        <span class="status-dot"></span>
                        Blockchain belum terhubung
                    </div>

                    <div class="topbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                </div>

            </header>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            {{-- PAGE CONTENT --}}
            <section class="page-content">
                @yield('content')
            </section>

            {{-- FOOTER --}}
            <footer class="footer">
                SKINVENTORY © {{ date('Y') }}
                · Skincare Inventory Management System
            </footer>

        </main>

    </div>

</body>
</html>