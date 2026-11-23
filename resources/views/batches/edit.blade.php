<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Batch - SKINVENTORY</title>
</head>
<body>
    <h1>Edit Batch</h1>

    <a href="{{ route('batches.index') }}">Kembali</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('batches.update', $batch->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Produk</label><br>
            <select name="product_id" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id', $batch->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} ({{ $product->sku }})
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Kode Batch</label><br>
            <input type="text" name="batch_code"
                value="{{ old('batch_code', $batch->batch_code) }}" required>
        </div>

        <br>

        <div>
            <label>Tanggal Produksi</label><br>
            <input type="date" name="manufacturing_date"
                value="{{ old('manufacturing_date', $batch->manufacturing_date?->format('Y-m-d')) }}">
        </div>

        <br>

        <div>
            <label>Tanggal Kedaluwarsa</label><br>
            <input type="date" name="expiry_date"
                value="{{ old('expiry_date', $batch->expiry_date?->format('Y-m-d')) }}" required>
        </div>

        <br>

        <div>
            <label>Status Batch</label><br>
            <select name="status" required>
                <option value="aktif"
                    {{ old('status', $batch->status) == 'aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="nonaktif"
                    {{ old('status', $batch->status) == 'nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>
            </select>
        </div>

        <br>

        <p>Stok saat ini: <strong>{{ $batch->quantity }}</strong></p>
        <small>Perubahan stok dilakukan melalui pencatatan stok, bukan melalui edit batch.</small>

        <br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>