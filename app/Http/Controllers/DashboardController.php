<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $lowStock = Product::whereColumn('quantity', '<=', 'reorder_level')->count();
        $outOfStock = Product::where('quantity', 0)->count();

        // Calculate total value
        $inventoryValue = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total');

        return view('dashboard', compact('totalProducts', 'lowStock', 'outOfStock', 'inventoryValue'));
    }
}
