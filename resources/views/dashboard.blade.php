@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard Inventaris')
@section('page-description', 'Ringkasan aktivitas dan kondisi inventaris produk skincare.')

@section('content')

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:15px; margin-bottom:20px;">

    <div class="stat-card">
        <div class="muted">Total Produk</div>
        <h2 style="font-size:27px; margin-top:13px;">{{ number_format($totalProducts) }}</h2>
        <small class="muted">Produk terdaftar</small>
    </div>

    <div class="stat-card">
        <div class="muted">Total Stok</div>
        <h2 style="font-size:27px; margin-top:13px;">{{ number_format($totalStock) }}</h2>
        <small class="muted">Unit persediaan</small>
    </div>

    <div class="stat-card">
        <div class="muted">Transaksi Stok</div>
        <h2 style="font-size:27px; margin-top:13px;">{{ number_format($totalTransactions) }}</h2>
        <small class="muted">Transaksi tercatat</small>
    </div>

    <div class="stat-card">
        <div class="muted">Batch Mendekati Kedaluwarsa</div>
        <h2 style="font-size:27px; margin-top:13px;">{{ $expiringBatches->count() }}</h2>
        <small class="muted">Dalam 60 hari ke depan</small>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h2>Batch Mendekati Kedaluwarsa</h2>
        <a href="{{ route('batches.index') }}" class="btn btn-secondary">Lihat Semua Batch</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>KODE BATCH</th>
                    <th>NAMA PRODUK</th>
                    <th>TANGGAL KEDALUWARSA</th>
                    <th>SISA STOK</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expiringBatches as $batch)
                    <tr>
                        <td>{{ $batch->batch_code }}</td>
                        <td>{{ $batch->product->name ?? '-' }}</td>
                        <td>{{ $batch->expiry_date->format('d/m/Y') }}</td>
                        <td>{{ number_format($batch->quantity) }}</td>
                        <td><span class="badge badge-warning">Mendekati Kedaluwarsa</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">Tidak ada batch yang mendekati kedaluwarsa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h2>Transaksi Stok Terbaru</h2>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>KODE BATCH</th>
                    <th>PRODUK</th>
                    <th>JENIS TRANSAKSI</th>
                    <th>JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaction->batch->batch_code ?? '-' }}</td>
                        <td>{{ $transaction->batch->product->name ?? '-' }}</td>
                        <td>{{ ucfirst($transaction->type ?? '-') }}</td>
                        <td>{{ number_format($transaction->quantity ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">Belum ada transaksi stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection