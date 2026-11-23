<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->batches()->exists()) {
            return back()->with(
                'error',
                'Produk tidak dapat dihapus karena masih memiliki batch.'
            );
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function index(Request $request)
{
    $query = Product::query()->withSum('batches', 'quantity');

    // Pencarian nama, SKU, atau merek
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%");
        });
    }

    // Filter kategori
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    // Filter status produk
    if ($request->filled('active')) {
        $query->where('is_active', $request->active === '1');
    }

    $products = $query->orderBy('name')
        ->paginate(10)
        ->withQueryString();

    // Data ringkasan
    $totalProducts = Product::count();

    $totalStock = \App\Models\Batch::sum('quantity');

    $activeCategories = Product::where('is_active', true)
        ->whereNotNull('category')
        ->distinct('category')
        ->count('category');

    $activeProducts = Product::where('is_active', true)->count();

    $categories = Product::whereNotNull('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    // Distribusi produk dan stok berdasarkan kategori
    $categoryStats = Product::query()
        ->select('category')
        ->selectRaw('COUNT(*) as total_products')
        ->withSum('batches', 'quantity')
        ->whereNotNull('category')
        ->groupBy('category')
        ->orderBy('category')
        ->get();

    return view('products.index', compact(
        'products',
        'totalProducts',
        'totalStock',
        'activeCategories',
        'activeProducts',
        'categories',
        'categoryStats'
    ));
}

public function export()
{
    $products = Product::withSum('batches', 'quantity')
        ->orderBy('name')
        ->get();

    return response()->streamDownload(function () use ($products) {
        $file = fopen('php://output', 'w');

        // BOM agar karakter UTF-8 terbaca dengan baik di Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'SKU',
            'Nama Produk',
            'Merek',
            'Kategori',
            'Satuan',
            'Total Stok',
            'Status'
        ]);

        foreach ($products as $product) {
            fputcsv($file, [
                $product->sku,
                $product->name,
                $product->brand,
                $product->category,
                $product->unit,
                $product->batches_sum_quantity ?? 0,
                $product->is_active ? 'Aktif' : 'Nonaktif',
            ]);
        }

        fclose($file);
    }, 'data-produk-skinventory.csv', [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}
}