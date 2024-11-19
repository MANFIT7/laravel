<?php

namespace Database\Seeders;

use App\Models\Sales;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $sales = [
            [
                'invoice_number' => 'INV-2024-001',
                'sale_date' => '2024-01-01',
                'customer_name' => 'Budi Santoso',
                'total_amount' => 1500000,
                'notes' => 'Penjualan pertama tahun 2024',
                'status' => 'completed'
            ],
            [
                'invoice_number' => 'INV-2024-002',
                'sale_date' => '2024-01-02',
                'customer_name' => 'Siti Rahayu',
                'total_amount' => 2750000,
                'notes' => 'Pembelian dalam jumlah besar',
                'status' => 'pending'
            ],
            [
                'invoice_number' => 'INV-2024-003',
                'sale_date' => '2024-01-03',
                'customer_name' => 'Ahmad Wijaya',
                'total_amount' => 500000,
                'notes' => 'Pesanan kecil',
                'status' => 'cancelled'
            ]
        ];

        foreach ($sales as $sale) {
            Sales::create($sale);
        }
    }
}
