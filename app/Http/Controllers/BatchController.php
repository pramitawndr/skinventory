<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::with('product');

        // Pencarian berdasarkan kode batch atau nama produk
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('batch_code', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter produk
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter kondisi berdasarkan tanggal kedaluwarsa
        if ($request->status === 'aman') {
            $query->whereDate('expiry_date', '>', today()->addDays(90));
        } elseif ($request->status === 'segera') {
            $query->whereDate('expiry_date', '>=', today())
                ->whereDate('expiry_date', '<=', today()->addDays(90));
        } elseif ($request->status === 'expired') {
            $query->whereDate('expiry_date', '<', today());
        }

        $batches = $query->orderBy('expiry_date')->paginate(10)->withQueryString();

        // Ringkasan seluruh data batch, tidak terpengaruh filter
        $totalBatches = Batch::count();

        $safeBatches = Batch::whereDate(
            'expiry_date',
            '>',
            today()->addDays(90)
        )->count();

        $expiringBatches = Batch::whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->addDays(90))
            ->count();

        $expiredBatches = Batch::whereDate('expiry_date', '<', today())->count();

        $products = Product::orderBy('name')->get();

        return view('batches.index', compact(
            'batches',
            'products',
            'totalBatches',
            'safeBatches',
            'expiringBatches',
            'expiredBatches'
        ));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('batches.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_code' => 'required|string|max:255|unique:batches,batch_code',
            'manufacturing_date' => 'nullable|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date',
            'quantity' => 'required|integer|min:0',
        ]);

        $validated['status'] = 'aktif';

        Batch::create($validated);

        return redirect()->route('batches.index')
            ->with('success', 'Batch berhasil ditambahkan.');
    }

    public function edit(Batch $batch)
    {
        $products = Product::where('is_active', true)
            ->orWhere('id', $batch->product_id)
            ->orderBy('name')
            ->get();

        return view('batches.edit', compact('batch', 'products'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('batches', 'batch_code')->ignore($batch->id),
            ],
            'manufacturing_date' => 'nullable|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Quantity tidak diubah melalui form edit batch.
        $batch->update($validated);

        return redirect()->route('batches.index')
            ->with('success', 'Data batch berhasil diperbarui.');
    }

    public function destroy(Batch $batch)
    {
        if ($batch->transactions()->exists()) {
            return redirect()->route('batches.index')
                ->with('error', 'Batch tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $batch->delete();

        return redirect()->route('batches.index')
            ->with('success', 'Batch berhasil dihapus.');
    }
}