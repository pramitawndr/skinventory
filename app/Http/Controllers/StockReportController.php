<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\StockReport;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockReportController extends Controller
{
    // Menampilkan daftar pengajuan
    
    public function index()
    {
        $query = StockReport::with([
            'batch.product',
            'user',
            'reviewer',
            'stockTransaction',
        ]);

        if (auth()->user()->role === 'admin') {
            $query->where('user_id', auth()->id());
        }

        $totalReports = (clone $query)->count();

        $pendingReports = (clone $query)
            ->where('status', 'pending')->count();

        $approvedReports = (clone $query)
            ->where('status', 'approved')->count();

        $rejectedReports = (clone $query)
            ->where('status', 'rejected')->count();

        $reports = $query->latest()->paginate(10);

        return view('stock-reports.index', compact(
            'reports',
            'totalReports',
            'pendingReports',
            'approvedReports',
            'rejectedReports'
        ));
    }


    // Menampilkan form pengajuan
    public function create()
    {
        $batches = Batch::with('product')
            ->where('status', 'aktif')
            ->whereDate('expiry_date', '>=', today())
            ->orderBy('expiry_date')
            ->get();

        return view('stock-reports.create', compact('batches'));
    }

    // Menyimpan pengajuan Admin
    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => ['required', 'exists:batches,id'],
            'type' => ['required', 'in:masuk,keluar'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);

        if (
            $validated['type'] === 'keluar' &&
            $validated['quantity'] > $batch->quantity
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => 'Jumlah stok keluar melebihi stok batch yang tersedia.'
                ]);
        }

        StockReport::create([
            'batch_id' => $validated['batch_id'],
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('stock-reports.index')
            ->with('success', 'Pengajuan transaksi berhasil dikirim dan menunggu persetujuan Auditor.');
    }

    // Menyetujui pengajuan
    public function approve(StockReport $stockReport)
    {
        if ($stockReport->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $approved = DB::transaction(function () use ($stockReport) {
            $report = StockReport::whereKey($stockReport->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($report->status !== 'pending') {
                return false;
            }

            $batch = Batch::whereKey($report->batch_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Periksa kembali stok saat Auditor menyetujui.
            if (
                $report->type === 'keluar' &&
                $report->quantity > $batch->quantity
            ) {
                return false;
            }

            // Perbarui stok batch.
            if ($report->type === 'masuk') {
                $batch->increment('quantity', $report->quantity);
            } else {
                $batch->decrement('quantity', $report->quantity);
            }

            // Buat catatan transaksi final.
            $transaction = StockTransaction::create([
                'batch_id' => $batch->id,
                'user_id' => $report->user_id,
                'transaction_code' => 'TXN-' .
                    now()->format('YmdHis') . '-' .
                    Str::upper(Str::random(4)),
                'type' => $report->type,
                'quantity' => $report->quantity,
                'blockchain_tx_hash' => null,
                'notes' => $report->notes,
            ]);

            // Tandai pengajuan sebagai disetujui.
            $report->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'stock_transaction_id' => $transaction->id,
            ]);

            return true;
        });

        if (!$approved) {
            return back()->with(
                'error',
                'Persetujuan gagal. Stok batch mungkin tidak mencukupi atau pengajuan sudah diproses.'
            );
        }

        return back()->with(
            'success',
            'Pengajuan disetujui dan stok berhasil diperbarui.'
        );
    }

    // Menolak pengajuan
    public function reject(Request $request, StockReport $stockReport)
    {
        $validated = $request->validate([
            'reviewer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($stockReport->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $stockReport->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_note' => $validated['reviewer_note'] ?? null,
        ]);

        return back()->with(
            'success',
            'Pengajuan berhasil ditolak. Jumlah stok tidak berubah.'
        );
    }
}