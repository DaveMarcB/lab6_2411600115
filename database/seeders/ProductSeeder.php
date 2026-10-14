<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Portland Cement', 'sku' => 'LUM-001', 'category' => 'Lumber', 'quantity' => 50, 'reorder_level' => 20, 'unit_price' => 250.00, 'supplier' => 'BuildIt Co'],
            ['name' => 'Plywood 1/2"', 'sku' => 'LUM-002', 'category' => 'Lumber', 'quantity' => 10, 'reorder_level' => 15, 'unit_price' => 850.00, 'supplier' => 'WoodWorks'],
            ['name' => 'Claw Hammer', 'sku' => 'TOL-001', 'category' => 'Tools', 'quantity' => 25, 'reorder_level' => 5, 'unit_price' => 350.00, 'supplier' => 'ToolMaster'],
            ['name' => 'Power Drill 20V', 'sku' => 'TOL-002', 'category' => 'Tools', 'quantity' => 2, 'reorder_level' => 5, 'unit_price' => 4500.00, 'supplier' => 'ToolMaster'],
            ['name' => 'PVC Pipe 2"', 'sku' => 'PLU-001', 'category' => 'Plumbing', 'quantity' => 100, 'reorder_level' => 30, 'unit_price' => 180.00, 'supplier' => 'AquaFlow'],
            ['name' => 'Teflon Tape', 'sku' => 'PLU-002', 'category' => 'Plumbing', 'quantity' => 200, 'reorder_level' => 50, 'unit_price' => 25.00, 'supplier' => 'AquaFlow'],
            ['name' => 'Electrical Wire THHN 12', 'sku' => 'ELE-001', 'category' => 'Electrical', 'quantity' => 0, 'reorder_level' => 10, 'unit_price' => 3500.00, 'supplier' => 'VoltCo'],
            ['name' => 'LED Bulb 9W', 'sku' => 'ELE-002', 'category' => 'Electrical', 'quantity' => 80, 'reorder_level' => 20, 'unit_price' => 120.00, 'supplier' => 'VoltCo'],
            ['name' => 'Latex Paint White', 'sku' => 'PNT-001', 'category' => 'Paint', 'quantity' => 15, 'reorder_level' => 10, 'unit_price' => 650.00, 'supplier' => 'ColorMax'],
            ['name' => 'Paint Brush 2"', 'sku' => 'PNT-002', 'category' => 'Paint', 'quantity' => 45, 'reorder_level' => 15, 'unit_price' => 85.00, 'supplier' => 'ColorMax'],
            ['name' => 'Roofing Nails 2"', 'sku' => 'HDW-001', 'category' => 'Hardware', 'quantity' => 150, 'reorder_level' => 50, 'unit_price' => 95.00, 'supplier' => 'IronFix'],
            ['name' => 'Steel Rebar 10mm', 'sku' => 'HDW-002', 'category' => 'Hardware', 'quantity' => 300, 'reorder_level' => 100, 'unit_price' => 165.00, 'supplier' => 'IronFix'],
            ['name' => 'Safety Goggles', 'sku' => 'SAF-001', 'category' => 'Hardware', 'quantity' => 4, 'reorder_level' => 10, 'unit_price' => 150.00, 'supplier' => 'SafeGear'],
            ['name' => 'Work Gloves', 'sku' => 'SAF-002', 'category' => 'Hardware', 'quantity' => 60, 'reorder_level' => 20, 'unit_price' => 80.00, 'supplier' => 'SafeGear'],
            ['name' => 'Measuring Tape 5m', 'sku' => 'TOL-003', 'category' => 'Tools', 'quantity' => 30, 'reorder_level' => 10, 'unit_price' => 220.00, 'supplier' => 'ToolMaster'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
