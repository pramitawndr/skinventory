@extends('layouts.app')

@section('title', 'Data Batch')
@section('breadcrumb', 'Data Batch')
@section('page-title', 'Data Batch')
@section('page-description', 'Registrasi dan pelacakan batch produksi skincare.')

@section('content')

{{-- RINGKASAN --}}
<div class="batch-stats">

    <div class="stat-card">
        <div class="stat-label">TOTAL BATCH TERDAFTAR</div>
        <div class="stat-number">{{ number_format($totalBatches) }}</div>
        <div class="stat-note">Seluruh batch dalam sistem</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">BATCH AMAN</div>
        <div class="stat-number green">{{ number_format($safeBatches) }}</div>
        <div class="stat-note">Kedaluwarsa lebih dari 90 hari</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">SEGERA KEDALUWARSA</div>
        <div class="stat-number orange">{{ number_format($expiringBatches) }}</div>
        <div class="stat-note">Kedaluwarsa dalam 90 hari</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">SUDAH KEDALUWARSA</div>
        <div class="stat-number red">{{ number_format($expiredBatches) }}</div>
        <div class="stat-note">Perlu tindak lanjut</div>
    </div>

</div>

{{-- FILTER DAN PENCARIAN --}}
<div class="panel batch-filter-panel">
    <form action="{{ route('batches.index') }}" method="GET" class="batch-filter-form">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Cari kode batch atau nama produk..."
            value="{{ request('search') }}"
        >

        <select name="status" class="form-control">
            <option value="">Semua Status</option>
            <option value="aman" {{ request('status') === 'aman' ? 'selected' : '' }}>
                Aman
            </option>
            <option value="segera" {{ request('status') === 'segera' ? 'selected' : '' }}>
                Segera Kedaluwarsa
            </option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>
                Kedaluwarsa
            </option>
        </select>

        <select name="product_id" class="form-control">
            <option value="">Semua Produk</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Cari / Filter</button>

        <a href="{{ route('batches.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- TABEL BATCH --}}
<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Daftar Batch Produksi</h2>
            <p class="muted" style="margin-top:6px;">
                Informasi stok dan kondisi kedaluwarsa setiap batch.
            </p>
        </div>

        @if(auth()->user()->role === 'auditor')
            <a href="{{ route('batches.create') }}" class="btn btn-primary">
                + Tambah Batch
            </a>
        @endif
    </div>

    <div class="table-wrap">
        <table class="batch-table">
            <thead>
                <tr>
                    <th>KODE BATCH</th>
                    <th>NAMA PRODUK</th>
                    <th>JUMLAH STOK</th>
                    <th>TANGGAL KEDALUWARSA</th>
                    <th>STATUS KONDISI</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                @forelse($batches as $batch)
                    @php
                        $daysLeft = today()->diffInDays($batch->expiry_date, false);

                        if ($batch->status === 'nonaktif') {
                            $condition = 'Nonaktif';
                            $badgeClass = 'badge-neutral';
                        } elseif ($daysLeft < 0) {
                            $condition = 'Kedaluwarsa';
                            $badgeClass = 'badge-expired';
                        } elseif ($daysLeft <= 90) {
                            $condition = 'Segera Kedaluwarsa';
                            $badgeClass = 'badge-warning';
                        } else {
                            $condition = 'Aman';
                            $badgeClass = 'badge-success';
                        }
                    @endphp

                    <tr>
                        <td>
                            <strong class="batch-code">{{ $batch->batch_code }}</strong>
                            <small class="table-subtext">
                                {{ $batch->manufacturing_date
                                    ? 'Produksi: '.$batch->manufacturing_date->format('d M Y')
                                    : 'Tanggal produksi tidak tersedia' }}
                            </small>
                        </td>

                        <td>
                            <strong>{{ $batch->product->name ?? 'Produk tidak tersedia' }}</strong>
                            <small class="table-subtext">
                                {{ $batch->product->sku ?? '-' }}
                            </small>
                        </td>

                        <td>
                            <strong>{{ number_format($batch->quantity) }}</strong>
                            <small class="table-subtext">
                                {{ $batch->product->unit ?? 'Unit' }}
                            </small>
                        </td>

                        <td>
                            {{ $batch->expiry_date->format('d M Y') }}
                            <small class="table-subtext">
                                @if($daysLeft < 0)
                                    Telah lewat {{ abs($daysLeft) }} hari
                                @else
                                    {{ $daysLeft }} hari tersisa
                                @endif
                            </small>
                        </td>

                        <td>
                            <span class="badge {{ $badgeClass }}">
                                {{ $condition }}
                            </span>
                        </td>

                        <td>
                            @if(auth()->user()->role === 'auditor')
                                <div class="table-actions">
                                    <a href="{{ route('batches.edit', $batch) }}"
                                       class="btn btn-secondary">Edit</a>

                                    <form action="{{ route('batches.destroy', $batch) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus batch ini?')">
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
                            Tidak ada data batch yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="batch-pagination">
        <span class="muted">
            Menampilkan {{ $batches->firstItem() ?? 0 }}–{{ $batches->lastItem() ?? 0 }}
            dari {{ $batches->total() }} batch
        </span>

        {{ $batches->links() }}
    </div>
</div>

{{-- INFORMASI INTEGRITAS --}}
<div class="integrity-panel">
    <div class="integrity-icon">◇</div>
    <div>
        <strong>Integritas Data Batch</strong>
        <p>
            Data batch dikelola melalui aplikasi SKINVENTORY.
            Pencatatan dan verifikasi blockchain akan tersedia setelah integrasi smart contract selesai.
        </p>
    </div>
    <span class="badge badge-warning">Belum terhubung</span>
</div>

@endsection