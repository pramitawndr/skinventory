@extends('layouts.app')

@section('content')
<div class="page-container">
    <div class="page-heading">
        <div>
            <h1>Transaksi Stok</h1>
            <p>Ajukan pencatatan stok masuk atau stok keluar berdasarkan batch produk.</p>
        </div>

        <a href="{{ route('stock-reports.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <h2>Form Pengajuan Transaksi</h2>
        <p class="muted">
            Pengajuan akan dikirim kepada Auditor untuk diperiksa sebelum stok diperbarui.
        </p>

        <form action="{{ route('stock-reports.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="type">Jenis Transaksi</label>
                <select name="type" id="type" required>
                    <option value="">Pilih jenis transaksi</option>
                    <option value="masuk" {{ old('type') === 'masuk' ? 'selected' : '' }}>
                        Stok Masuk
                    </option>
                    <option value="keluar" {{ old('type') === 'keluar' ? 'selected' : '' }}>
                        Stok Keluar
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="batch_id">Produk dan Batch</label>
                <select name="batch_id" id="batch_id" required>
                    <option value="">Pilih batch produk</option>

                    @foreach ($batches as $batch)
                        <option
                            value="{{ $batch->id }}"
                            data-stock="{{ $batch->quantity }}"
                            {{ old('batch_id') == $batch->id ? 'selected' : '' }}
                        >
                            {{ $batch->product->name }}
                            — {{ $batch->batch_code }}
                            (Stok: {{ number_format($batch->quantity) }})
                        </option>
                    @endforeach
                </select>
                <small id="stock-information" class="muted">
                    Pilih batch untuk melihat stok saat ini.
                </small>
            </div>

            <div class="form-group">
                <label for="quantity">Jumlah Transaksi</label>
                <input
                    type="number"
                    name="quantity"
                    id="quantity"
                    min="1"
                    value="{{ old('quantity') }}"
                    placeholder="Masukkan jumlah stok"
                    required
                >
            </div>

            <div class="form-group">
                <label for="notes">Keterangan</label>
                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    placeholder="Tuliskan alasan atau keterangan transaksi..."
                >{{ old('notes') }}</textarea>
            </div>

            <div class="info-panel">
                <strong>Informasi Persetujuan</strong>
                <p>
                    Data yang dikirim belum mengubah jumlah stok.
                    Perubahan stok dilakukan setelah Auditor menyetujui pengajuan.
                </p>
                <span class="status-badge pending">Menunggu Persetujuan</span>
            </div>

            <div class="form-actions">
                <a href="{{ route('stock-reports.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const batchSelect = document.getElementById('batch_id');
    const stockInformation = document.getElementById('stock-information');

    batchSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];

        if (selected.value) {
            stockInformation.textContent =
                'Stok batch saat ini: ' + selected.dataset.stock + ' unit';
        } else {
            stockInformation.textContent =
                'Pilih batch untuk melihat stok saat ini.';
        }
    });

    batchSelect.dispatchEvent(new Event('change'));
</script>
@endsection