@extends('layouts.app')

@section('content')
<div class="page-container">
    <div class="stats-grid">
        <div class="stat-card">
            <span>Total Transaksi</span>
            <h2>{{ number_format($totalTransactions) }}</h2>
        </div>

        <div class="stat-card">
            <span>Total Stok Masuk</span>
            <h2>{{ number_format($totalIncoming) }}</h2>
            <small>Unit</small>
        </div>

        <div class="stat-card">
            <span>Total Stok Keluar</span>
            <h2>{{ number_format($totalOutgoing) }}</h2>
            <small>Unit</small>
        </div>

        <div class="stat-card">
            <span>Selisih Stok</span>
            <h2>{{ number_format($totalIncoming - $totalOutgoing) }}</h2>
            <small>Unit</small>
        </div>
    </div>

    <div class="table-card">

        <div class="table-heading">
            <div>
                <h2>Daftar Transaksi</h2>
                <p class="muted">
                    Riwayat pencatatan stok masuk dan stok keluar.
                </p>
            </div>
        </div>

        <form method="GET"
              action="{{ route('stock-transactions.index') }}"
              class="filter-form">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari kode transaksi, batch, SKU, atau produk..."
            >

            <select name="type">
                <option value="">Semua Jenis Transaksi</option>
                <option value="masuk"
                    {{ request('type') === 'masuk' ? 'selected' : '' }}>
                    Stok Masuk
                </option>
                <option value="keluar"
                    {{ request('type') === 'keluar' ? 'selected' : '' }}>
                    Stok Keluar
                </option>
            </select>

            <button type="submit" class="btn btn-primary">
                Cari
            </button>

            <a href="{{ route('stock-transactions.index') }}"
               class="btn btn-secondary">
                Reset
            </a>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Kode Batch</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Dicatat Oleh</th>
                        <th>Status Blockchain</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>
                                <strong>{{ $transaction->transaction_code }}</strong>
                            </td>

                            <td>
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $transaction->batch->product->name }}
                            </td>

                            <td>
                                {{ $transaction->batch->batch_code }}
                            </td>

                            <td>
                                @if ($transaction->type === 'masuk')
                                    <span class="status-badge approved">
                                        Stok Masuk
                                    </span>
                                @else
                                    <span class="status-badge rejected">
                                        Stok Keluar
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ number_format($transaction->quantity) }} unit
                            </td>

                            <td>
                                {{ $transaction->user->name ?? 'Pengguna tidak tersedia' }}
                            </td>

                            <td>
                                @if ($transaction->blockchain_tx_hash)
                                    <span class="status-badge approved">
                                        Tercatat
                                    </span>
                                @else
                                    <span class="status-badge pending">
                                        Belum terhubung
                                    </span>
                                @endif
                            </td>
                        </tr>

                        @if ($transaction->notes)
                            <tr>
                                <td colspan="8">
                                    <small class="muted">
                                        Keterangan: {{ $transaction->notes }}
                                    </small>
                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                Belum ada transaksi stok yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $transactions->links() }}
        </div>
    </div>

    <div class="info-panel">
        <h3>Informasi Integritas Blockchain</h3>
        <p>
            Halaman ini menampilkan transaksi stok final dari database aplikasi.
            Status blockchain akan berubah setelah integrasi smart contract
            berhasil dilakukan dan transaction hash tersimpan.
        </p>
        <span class="status-badge pending">
            Integrasi Blockchain Belum Aktif
        </span>
    </div>

</div>
@endsection