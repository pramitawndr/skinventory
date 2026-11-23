@extends('layouts.app')

@section('title', 'Data Produk')
@section('breadcrumb', 'Data Produk')
@section('page-title', 'Data Produk')
@section('page-description', 'Kelola master katalog skincare, kategori produk, dan pantau stok terakumulasi.')

@section('content')

{{-- RINGKASAN --}}
<div class="product-stats">

    <div class="stat-card">
        <div class="stat-label">TOTAL SKU TERDAFTAR</div>
        <div class="stat-number">{{ number_format($totalProducts) }} Produk</div>
        <div class="stat-note">Seluruh produk dalam sistem</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">AKUMULASI FISIK GUDANG</div>
        <div class="stat-number">{{ number_format($totalStock) }} Unit</div>
        <div class="stat-note">Total stok dari seluruh batch</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">KATEGORI AKTIF</div>
        <div class="stat-number">{{ number_format($activeCategories) }} Kategori</div>
        <div class="stat-note">{{ number_format($activeProducts) }} produk aktif</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">STATUS SMART CONTRACT</div>
        <div class="stat-number" style="font-size:19px;">Belum Terhubung</div>
        <div class="stat-note">Menunggu integrasi blockchain</div>
    </div>

</div>

{{-- JUDUL DAN AKSI --}}
<div class="product-title-row">
    <div>
        <span class="section-kicker">KATALOG INVENTARIS UTAMA</span>
        <h2>Daftar Produk Skincare</h2>
        <p class="muted">Kelola informasi produk dan pantau jumlah stok dari seluruh batch.</p>
    </div>

    <div class="product-header-actions">
        <a href="{{ route('products.export') }}" class="btn btn-secondary">
            ↓ Ekspor CSV
        </a>

        @if(auth()->user()->role === 'auditor')
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                + Tambah Produk
            </a>
        @endif
    </div>
</div>

{{-- PENCARIAN DAN FILTER --}}
<div class="panel product-filter-panel">
    <form action="{{ route('products.index') }}" method="GET" class="product-filter-form">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Cari berdasarkan nama atau kode produk..."
            value="{{ request('search') }}"
        >

        <select name="category" class="form-control">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category }}"
                    {{ request('category') == $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
            @endforeach
        </select>

        <select name="active" class="form-control">
            <option value="">Semua Status</option>
            <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>
                Aktif
            </option>
            <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>
                Nonaktif
            </option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- TABEL PRODUK --}}
<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Katalog Inventaris Produk</h2>
            <p class="muted" style="margin-top:6px;">
                Menampilkan {{ $products->total() }} produk berdasarkan data sistem.
            </p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="product-table">
            <thead>
                <tr>
                    <th>KODE PRODUK</th>
                    <th>NAMA PRODUK</th>
                    <th>KATEGORI</th>
                    <th>TOTAL STOK</th>
                    <th>STATUS LEDGER</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <strong class="product-sku">{{ $product->sku }}</strong>
                            <small class="table-subtext">{{ $product->brand ?? 'Tanpa merek' }}</small>
                        </td>

                        <td>
                            <strong>{{ $product->name }}</strong>
                            <small class="table-subtext">
                                {{ $product->unit ?? 'Unit' }}
                                @if(!$product->is_active)
                                    · Produk nonaktif
                                @endif
                            </small>
                        </td>

                        <td>
                            <span class="category-chip">{{ $product->category ?? 'Lainnya' }}</span>
                        </td>

                        <td>
                            <strong>{{ number_format($product->batches_sum_quantity ?? 0) }}</strong>
                            <small class="table-subtext">{{ $product->unit ?? 'Unit' }}</small>
                        </td>

                        <td>
                            <span class="ledger-status">
                                <span class="ledger-dot"></span>
                                Belum terhubung
                            </span>
                        </td>

                        <td>
                            @if(auth()->user()->role === 'auditor')
                                <div class="table-actions">
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn btn-secondary">Edit</a>

                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="muted">Lihat saja</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            Tidak ada produk yang sesuai dengan pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="batch-pagination">
        <span class="muted">
            Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
            dari {{ $products->total() }} produk
        </span>

        {{ $products->links() }}
    </div>
</div>

{{-- PANEL INFORMASI --}}
<div class="product-bottom-grid">

    <div class="panel certification-panel">
        <div class="panel-heading">
            <h2>Integritas Sertifikasi Produk</h2>
            <span class="badge badge-warning">Belum terintegrasi</span>
        </div>

        <p class="muted certification-description">
            Informasi sertifikasi BPOM dan halal dapat disiapkan sebagai metadata produk.
            Pencatatan hash dan verifikasi on-chain akan dilakukan setelah smart contract terintegrasi.
        </p>

        <div class="certification-steps">
            <div class="certification-step">
                <span>1</span>
                <div>
                    <strong>Pendaftaran SKU</strong>
                    <small>Data produk disimpan di aplikasi</small>
                </div>
            </div>

            <div class="certification-arrow">→</div>

            <div class="certification-step">
                <span>2</span>
                <div>
                    <strong>Persiapan Metadata</strong>
                    <small>Informasi produk dan sertifikasi</small>
                </div>
            </div>

            <div class="certification-arrow">→</div>

            <div class="certification-step">
                <span>3</span>
                <div>
                    <strong>Verifikasi On-Chain</strong>
                    <small>Menunggu integrasi blockchain</small>
                </div>
            </div>
        </div>
    </div>

    <div class="panel category-panel">
        <div class="panel-heading">
            <h2>Distribusi Produk per Kategori</h2>
        </div>

        @php
            $maxCategory = max(1, $categoryStats->max('total_products') ?? 0);
        @endphp

        @forelse($categoryStats as $stat)
            <div class="category-stat-row">
                <div class="category-stat-heading">
                    <strong>{{ $stat->category }}</strong>
                    <span>{{ $stat->total_products }} produk</span>
                </div>

                <div class="category-progress">
                    <div style="width: {{ ($stat->total_products / $maxCategory) * 100 }}%;"></div>
                </div>
            </div>
        @empty
            <p class="muted">Belum ada data kategori.</p>
        @endforelse
    </div>

</div>

@endsection