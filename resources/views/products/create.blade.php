<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Produk | SKINVENTORY</title>
    <style>
        body { font-family:Arial,sans-serif; background:#f4f6f3; padding:30px; color:#333; }
        .container { max-width:650px; margin:auto; }
        .card { background:white; padding:28px; border-radius:12px; }
        h1 { color:#365b46; }
        label { display:block; margin:17px 0 7px; font-size:14px; }
        input,select,textarea { width:100%; padding:12px; border:1px solid #ddd; border-radius:7px; box-sizing:border-box; }
        textarea { min-height:90px; }
        .btn { padding:11px 16px; border:0; border-radius:7px; cursor:pointer; text-decoration:none; display:inline-block; }
        .primary { background:#365b46; color:white; }
        .secondary { background:#eee; color:#333; }
        .error { color:#c62828; font-size:13px; }
        .actions { margin-top:22px; display:flex; gap:10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Tambah Produk</h1>
        <p>Masukkan informasi produk skincare baru.</p>

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <label>SKU</label>
            <input type="text" name="sku" value="{{ old('sku') }}" required>

            <label>Nama Produk</label>
            <input type="text" name="name" value="{{ old('name') }}" required>

            <label>Brand</label>
            <input type="text" name="brand" value="{{ old('brand') }}" required>

            <label>Kategori</label>
            <select name="category" required>
                <option value="">Pilih kategori</option>
                @foreach(['Serum', 'Cleanser', 'Sunscreen', 'Moisturizer', 'Eye Cream', 'Toner', 'Other'] as $category)
                    <option value="{{ $category }}" @selected(old('category') === $category)>
                        {{ $category }}
                    </option>
                @endforeach
            </select>

            <label>Satuan</label>
            <input type="text" name="unit" value="{{ old('unit', 'Unit') }}" required>

            <label>Deskripsi</label>
            <textarea name="description">{{ old('description') }}</textarea>

            <input type="hidden" name="is_active" value="0">
            <label>
                <input type="checkbox" name="is_active" value="1"
                       style="width:auto"
                       @checked(old('is_active', '1') == '1')>
                Produk aktif
            </label>

            <div class="actions">
                <button type="submit" class="btn primary">Simpan Produk</button>
                <a href="{{ route('products.index') }}" class="btn secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>