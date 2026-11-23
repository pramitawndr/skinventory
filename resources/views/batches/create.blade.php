<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Batch - SKINVENTORY</title>
</head>
<body>
    <h1>Tambah Batch</h1>

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

    <form action="{{ route('batches.store') }}" method="POST">
        @csrf

        <div>
            <label>Produk</label><br>
            <select name="product_id" required>
                <option value="">-- Pilih Produk --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} ({{ $product->sku }})
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Kode Batch</label><br>
            <input type="text" name="batch_code"
                value="{{ old('batch_code') }}" required>
        </div>

        <br>

        <div>
            <label>Tanggal Produksi</label><br>
            <input type="date" name="manufacturing_date"
                value="{{ old('manufacturing_date') }}">
        </div>

        <br>

        <div>
            <label>Tanggal Kedaluwarsa</label><br>
            <input type="date" name="expiry_date"
                value="{{ old('expiry_date') }}" required>
        </div>

        <br>

        <div>
            <label>Stok Awal</label><br>
            <input type="number" name="quantity"
                value="{{ old('quantity', 0) }}" min="0" required>
        </div>

        <br>

        <button type="submit">Simpan Batch</button>
    </form>
</body>
</html>