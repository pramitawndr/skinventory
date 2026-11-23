<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SKINVENTORY</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #252b36;
        }
        .app { display: flex; min-height: 100vh; }
        .sidebar {
            width: 240px;
            background: #fff;
            padding: 28px 20px;
            border-right: 1px solid #e8eaf0;
        }
        .brand { font-size: 21px; font-weight: 700; margin-bottom: 42px; }
        .brand span { color: #a58bce; }
        .menu-title {
            font-size: 11px; color: #9ca3af;
            margin: 25px 0 12px; letter-spacing: 1px;
        }
        .menu a {
            display: block; padding: 12px 14px; margin-bottom: 6px;
            border-radius: 8px; text-decoration: none;
            color: #626b79; font-size: 14px;
        }
        .menu a.active { background: #f0eafb; color: #7955ad; font-weight: 600; }
        .main { flex: 1; min-width: 0; padding: 30px 36px; }
        .topbar {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 32px;
        }
        .topbar h1 { font-size: 25px; }
        .topbar p { color: #9299a5; font-size: 13px; margin-top: 7px; }
        .network {
            background: #fff; border: 1px solid #e8eaf0;
            padding: 10px 15px; border-radius: 8px; font-size: 12px;
        }
        .grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 18px; margin-bottom: 25px;
        }
        .card {
            background: #fff; border: 1px solid #eceef2;
            border-radius: 12px; padding: 22px;
        }
        .stat-label { color: #9299a5; font-size: 13px; }
        .stat-value { font-size: 28px; font-weight: 700; margin-top: 15px; }
        .section-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 20px; margin-bottom: 22px;
        }
        h2 { font-size: 16px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th {
            text-align: left; color: #9299a5; font-weight: 500;
            padding: 12px 8px; border-bottom: 1px solid #eee;
        }
        td { padding: 14px 8px; border-bottom: 1px solid #f0f1f4; }
        .empty { color: #9299a5; font-size: 13px; padding: 10px 0; }
        .category-row {
            display: flex; justify-content: space-between;
            padding: 12px 0; border-bottom: 1px solid #f0f1f4;
            font-size: 13px;
        }
        .status {
            display: inline-block; background: #fff3df;
            color: #b7791f; padding: 5px 9px;
            border-radius: 20px; font-size: 11px;
        }
        .wide { margin-bottom: 22px; overflow-x: auto; }
        @media(max-width: 1000px) {
            .grid { grid-template-columns: repeat(2, 1fr); }
            .section-grid { grid-template-columns: 1fr; }
        }
        @media(max-width: 650px) {
            .sidebar { width: 75px; padding: 20px 8px; }
            .brand { font-size: 12px; }
            .menu a { font-size: 0; text-align: center; padding: 12px 3px; }
            .main { padding: 20px 14px; }
            .grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .card { padding: 15px; }
            .stat-value { font-size: 22px; }
            .topbar { align-items: flex-start; gap: 10px; }
            .topbar h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">SKIN<span>VENTORY</span></div>
        <div class="menu-title">MAIN MENU</div>
        <nav class="menu">
            <a href="/dashboard" class="active">▦ &nbsp; Dashboard</a>
            <a href="#">▤ &nbsp; Products</a>
            <a href="#">▣ &nbsp; Batches</a>
            <a href="#">⇄ &nbsp; Stock Transactions</a>
        </nav>
        <div class="menu-title">MANAGEMENT</div>
        <nav class="menu">
            <a href="#">▧ &nbsp; Blockchain Registry</a>
            <a href="#">▤ &nbsp; Reports</a>
            <a href="#">⚙ &nbsp; Settings</a>
        </nav>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome to SKINVENTORY inventory management system.</p>
            </div>
            <div class="network">● Sepolia Testnet &nbsp; | &nbsp; Wallet Not Connected</div>
        </header>

        <section class="grid">
            <div class="card">
                <div class="stat-label">Total Products</div>
                <div class="stat-value">{{ $totalProducts }}</div>
            </div>
            <div class="card">
                <div class="stat-label">Total Stock</div>
                <div class="stat-value">{{ number_format($totalStock) }}</div>
            </div>
            <div class="card">
                <div class="stat-label">Stock Transactions</div>
                <div class="stat-value">{{ $totalTransactions }}</div>
            </div>
            <div class="card">
                <div class="stat-label">Expiring Batches</div>
                <div class="stat-value">{{ $expiringBatches->count() }}</div>
            </div>
        </section>

        <section class="section-grid">
            <div class="card">
                <h2>Product Categories</h2>
                @forelse($categoryStats as $category)
                    <div class="category-row">
                        <span>{{ $category->category }}</span>
                        <strong>{{ $category->total }}</strong>
                    </div>
                @empty
                    <p class="empty">No product categories yet.</p>
                @endforelse
            </div>

            <div class="card">
                <h2>Expiring Batches</h2>
                @forelse($expiringBatches as $batch)
                    <div class="category-row">
                        <div>
                            <strong>{{ $batch->product->name }}</strong>
                            <p class="empty">{{ $batch->batch_code }} · Expires {{ $batch->expiry_date->format('d M Y') }}</p>
                        </div>
                        <span class="status">{{ $batch->quantity }} units</span>
                    </div>
                @empty
                    <p class="empty">No batches expiring within 60 days.</p>
                @endforelse
            </div>
        </section>

        <section class="card wide">
            <h2>Recent Stock Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Transaction Code</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_code }}</td>
                            <td>{{ $transaction->batch->product->name }}</td>
                            <td>{{ $transaction->batch->batch_code }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                            <td>{{ $transaction->quantity }}</td>
                            <td>{{ $transaction->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="card">
            <h2>Smart Contract Registry</h2>
            <p class="empty">Blockchain integration will be connected in the next development stage.</p>
        </section>
    </main>
</div>
</body>
</html>