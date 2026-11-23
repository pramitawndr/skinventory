<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with([
            'batch.product',
            'user',
        ]);

        // Pencarian kode transaksi, batch, atau produk
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhereHas('batch', function ($batchQuery) use ($search) {
                        $batchQuery->where('batch_code', 'like', "%{$search}%")
                            ->orWhereHas('product', function ($productQuery) use ($search) {
                                $productQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('sku', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter jenis transaksi
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Ringkasan seluruh transaksi
        $totalTransactions = StockTransaction::count();

        $totalIncoming = StockTransaction::where('type', 'masuk')
            ->sum('quantity');

        $totalOutgoing = StockTransaction::where('type', 'keluar')
            ->sum('quantity');

        // Daftar transaksi
        $transactions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('stock-transactions.index', compact(
            'transactions',
            'totalTransactions',
            'totalIncoming',
            'totalOutgoing'
        ));
    }
}