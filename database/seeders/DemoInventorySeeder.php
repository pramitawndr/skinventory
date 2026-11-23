<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Batch;
use App\Models\StockTransaction;
use Illuminate\Database\Seeder;

class DemoInventorySeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'SKU-001',
                'name' => 'Glow Bright Niacinamide Serum',
                'brand' => 'Derm-Grade',
                'category' => 'Serum',
                'unit' => 'Unit',
            ],
            [
                'sku' => 'SKU-002',
                'name' => 'Gentle Cleanser',
                'brand' => 'Derm-Grade',
                'category' => 'Cleanser',
                'unit' => 'Unit',
            ],
            [
                'sku' => 'SKU-003',
                'name' => 'UV Shield Sunscreen Fluid SPF 50+',
                'brand' => 'Derm-Grade',
                'category' => 'Sunscreen',
                'unit' => 'Unit',
            ],
            [
                'sku' => 'SKU-004',
                'name' => 'Peptide Recovery Eye Cream',
                'brand' => 'Derm-Grade',
                'category' => 'Eye Cream',
                'unit' => 'Unit',
            ],
            [
                'sku' => 'SKU-005',
                'name' => 'Ceramide Barrier Moisturizer Gel',
                'brand' => 'Derm-Grade',
                'category' => 'Moisturizer',
                'unit' => 'Unit',
            ],
        ];

        foreach ($products as $item) {
            $product = Product::firstOrCreate(
                ['sku' => $item['sku']],
                $item
                );

            $batch = Batch::firstOrCreate([
                'batch_code' => 'BTH-' . str_pad($product->id, 4, '0', STR_PAD_LEFT)],
                [
                    'product_id' => $product->id,
                    'manufacturing_date' => now()->subMonths(6),
                    'expiry_date' => now()->addDays(42),
                    'quantity' => 120,
                    'status' => 'aktif',
                ]
            );

            StockTransaction::firstOrCreate(
                ['transaction_code' => 'TXN-' . str_pad($product->id, 4, '0', STR_PAD_LEFT)],
                [
                    'batch_id' => $batch->id,
                    'type' => 'masuk',
                    'quantity' => 120,
                    'notes' => 'Stok awal inventaris',
                ]
            );
        }
    }
}