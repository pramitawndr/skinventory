@extends('layouts.app')

@section('content')
<div class="page-container">
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('stock-reports.create') }}" class="btn btn-primary">
                + Buat Pengajuan
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <span>Total Pengajuan</span>
            <h2>{{ $totalReports }}</h2>
        </div>

        <div class="stat-card">
            <span>Menunggu Persetujuan</span>
            <h2>{{ $pendingReports }}</h2>
        </div>

        <div class="stat-card">
            <span>Disetujui</span>
            <h2>{{ $approvedReports }}</h2>
        </div>

        <div class="stat-card">
            <span>Ditolak</span>
            <h2>{{ $rejectedReports }}</h2>
        </div>
    </div>

    <div class="table-card">
        <div class="table-heading">
            <div>
                <h2>Riwayat Pengajuan Transaksi</h2>
                <p class="muted">
                    Daftar pengajuan stok masuk dan keluar beserta status persetujuannya.
                </p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Batch</th>
                        <th>Produk</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Pengaju</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        @if (auth()->user()->role === 'auditor')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->batch->batch_code }}</td>

                            <td>
                                {{ $report->batch->product->name }}
                            </td>

                            <td>
                                @if ($report->type === 'masuk')
                                    <span class="status-badge approved">Stok Masuk</span>
                                @else
                                    <span class="status-badge rejected">Stok Keluar</span>
                                @endif
                            </td>

                            <td>{{ number_format($report->quantity) }} unit</td>

                            <td>{{ $report->user->name ?? 'Pengguna tidak tersedia' }}</td>

                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>

                            <td>
                                @if ($report->status === 'pending')
                                    <span class="status-badge pending">Menunggu</span>
                                @elseif ($report->status === 'approved')
                                    <span class="status-badge approved">Disetujui</span>
                                @else
                                    <span class="status-badge rejected">Ditolak</span>
                                @endif
                            </td>

                            @if (auth()->user()->role === 'auditor')
                                <td>
                                    @if ($report->status === 'pending')
                                        <div class="action-group">
                                            <form
                                                action="{{ route('stock-reports.approve', $report) }}"
                                                method="POST"
                                                onsubmit="return confirm('Setujui pengajuan ini? Stok batch akan diperbarui.');"
                                            >
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    Setujui
                                                </button>
                                            </form>

                                            <form
                                                action="{{ route('stock-reports.reject', $report) }}"
                                                method="POST"
                                                onsubmit="return confirm('Tolak pengajuan ini?');"
                                            >
                                                @csrf
                                                <input
                                                    type="hidden"
                                                    name="reviewer_note"
                                                    value="Pengajuan ditolak oleh Auditor."
                                                >
                                                <button type="submit" class="btn btn-danger">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="muted">Sudah diproses</span>
                                    @endif
                                </td>
                            @endif
                        </tr>

                        @if ($report->notes || $report->reviewer_note)
                            <tr>
                                <td colspan="8">
                                    @if ($report->notes)
                                        <small>
                                            Keterangan pengaju: {{ $report->notes }}
                                        </small>
                                    @endif

                                    @if ($report->reviewer_note)
                                        <br>
                                        <small>
                                            Catatan Auditor: {{ $report->reviewer_note }}
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                Belum ada pengajuan transaksi stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $reports->links() }}
        </div>
    </div>

    <div class="info-panel">
        <h3>Status Pencatatan Blockchain</h3>
        <p>
            Saat ini pengajuan dan persetujuan transaksi masih diproses melalui
            database aplikasi. Integrasi smart contract dan pencatatan transaksi
            ke blockchain akan dilakukan pada tahap integrasi berikutnya.
        </p>
        <span class="status-badge pending">Blockchain Belum Terhubung</span>
    </div>

</div>
@endsection