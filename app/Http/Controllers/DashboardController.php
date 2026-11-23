<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Batch;
use App\Models\StockTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();

        $totalStock = Batch::sum('quantity');

        $totalTransactions = StockTransaction::count();

        $expiringBatches = Batch::with('product')
            ->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', today()->addDays(60))
            ->orderBy('expiry_date')
            ->get();

        $categoryStats = Product::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get();

        $recentTransactions = StockTransaction::with('batch.product')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalStock',
            'totalTransactions',
            'expiringBatches',
            'categoryStats',
            'recentTransactions'
        ));
    }
}