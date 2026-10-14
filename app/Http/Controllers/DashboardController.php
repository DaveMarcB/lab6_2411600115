<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $lowStock = Product::lowStock()->count();
        $outOfStock = Product::outOfStock()->count();

        // Calculate total value
        $inventoryValue = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        // Data for Part 7 dashboard sections
        $lowStockProducts = Product::lowStock()->orderBy('quantity', 'asc')->take(10)->get();
        $recentTransactions = InventoryTransaction::with(['product', 'user'])->recent()->take(10)->get();

        return view('dashboard', compact(
            'totalProducts',
            'lowStock',
            'outOfStock',
            'inventoryValue',
            'lowStockProducts',
            'recentTransactions'
        ));
    }

    /**
     * GET /api/dashboard/stats - Returns JSON with current statistics
     */
    public function apiStats(): JsonResponse
    {
        $totalProducts = Product::count();
        $lowStock = Product::lowStock()->count();
        $outOfStock = Product::outOfStock()->count();
        $inventoryValue = (float) (Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_products' => $totalProducts,
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
                'inventory_value' => $inventoryValue,
                'formatted_inventory_value' => '₱' . number_format($inventoryValue, 2),
                'timestamp' => now()->format('h:i:s A'),
            ]
        ]);
    }

    /**
     * GET /api/products/low-stock - Returns JSON array of low stock products
     */
    public function apiLowStock(): JsonResponse
    {
        $products = Product::lowStock()
            ->orderBy('quantity', 'asc')
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                    'reorder_level' => $product->reorder_level,
                    'status' => $product->stock_status,
                    'status_color' => $product->stock_status_color,
                    'url' => route('products.show', $product->id),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * GET /api/transactions/recent - Returns recent transactions
     */
    public function apiRecentTransactions(): JsonResponse
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->recent()
            ->take(10)
            ->get()
            ->map(function ($tx) {
                $prefix = $tx->type === 'stock_in' ? '+' : ($tx->type === 'stock_out' ? '-' : ($tx->quantity >= 0 ? '+' : ''));
                $qtyDisplay = $tx->type === 'adjustment' ? $prefix . $tx->quantity : $prefix . abs($tx->quantity);

                return [
                    'id' => $tx->id,
                    'date' => $tx->created_at->format('M d, Y h:i A'),
                    'product_name' => $tx->product->name ?? 'Deleted Product',
                    'product_url' => $tx->product ? route('products.show', $tx->product_id) : '#',
                    'type' => $tx->type,
                    'type_label' => ucwords(str_replace('_', ' ', $tx->type)),
                    'quantity_display' => $qtyDisplay,
                    'reference_number' => $tx->reference_number ?? 'N/A',
                    'user_name' => $tx->user->name ?? 'System',
                    'show_url' => route('transactions.show', $tx->id),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }
}