<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'products'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $transactionTypes = [
            'stock_in' => 'Stock In (Received / Returned)',
            'stock_out' => 'Stock Out (Sold / Used)',
            'adjustment' => 'Stock Adjustment',
        ];

        return view('transactions.create', compact('products', 'transactionTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|numeric|not_in:0',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->type === 'stock_out' && $request->filled('product_id')) {
                $product = Product::find($request->product_id);
                $requestedQty = abs((int) $request->quantity);

                if ($product && $requestedQty > $product->quantity) {
                    $validator->errors()->add(
                        'quantity',
                        "Insufficient stock for {$product->name}. Current available stock is {$product->quantity}."
                    );
                }
            }

            if (in_array($request->type, ['stock_in', 'stock_out']) && (int) $request->quantity < 1) {
                $validator->errors()->add(
                    'quantity',
                    'Quantity must be at least 1 for Stock In and Stock Out transactions.'
                );
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $validated = $validator->validated();

            if (in_array($validated['type'], ['stock_in', 'stock_out'])) {
                $validated['quantity'] = abs((int) $validated['quantity']);
            }

            $validated['user_id'] = Auth::id();

            InventoryTransaction::create($validated);

            return redirect()->route('transactions.index')
                ->with('success', 'Inventory transaction recorded and stock updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(InventoryTransaction $transaction)
    {
        try {
            $transaction->delete();
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction deleted and product stock reversed successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Part 8, Step 2: Show the Stock Adjustment Form.
     */
    public function createAdjustment()
    {
        $products = Product::orderBy('name')->get();
        $reasons = [
            'Physical count discrepancy' => 'Physical Count Discrepancy',
            'Damaged goods' => 'Damaged Goods',
            'Theft/loss' => 'Theft / Loss',
            'System error' => 'System Error',
            'Other' => 'Other',
        ];

        return view('transactions.adjust', compact('products', 'reasons'));
    }

    /**
     * Part 8, Step 3: Process the Stock Adjustment.
     */
    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'physical_count' => 'required|integer|min:0',
            'reason' => 'required|string|max:150',
            'notes' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $difference = (int) $validated['physical_count'] - (int) $product->quantity;

        if ($difference === 0) {
            return redirect()->back()
                ->withErrors(['physical_count' => "Physical count ({$validated['physical_count']}) already matches the current system quantity ({$product->quantity}). No adjustment needed."])
                ->withInput();
        }

        try {
            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $difference,
                'reference_number' => 'ADJ-' . now()->format('Ymd-His'),
                'notes' => "Reason: {$validated['reason']} (System: {$product->quantity} -> Physical: {$validated['physical_count']}). " . ($validated['notes'] ?? ''),
            ]);

            return redirect()->route('transactions.index')
                ->with('success', "Stock adjustment processed for {$product->name}. Difference of " . ($difference > 0 ? "+{$difference}" : $difference) . " applied.");
        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Part 9, Step 2: Generate Inventory Reports.
     */
    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // 1. Stock Movement Report within date range
        $stockMovements = InventoryTransaction::with(['product', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->recent()
            ->get();

        // 2. Low Stock Report
        $lowStockProducts = Product::lowStock()->orderBy('quantity', 'asc')->get();

        // 3. Inventory Valuation Report
        $allProducts = Product::orderBy('category')->orderBy('name')->get();
        $totalValuation = $allProducts->sum(fn($p) => $p->quantity * $p->unit_price);

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'stockMovements',
            'lowStockProducts',
            'allProducts',
            'totalValuation'
        ));
    }

    /**
     * Part 9, Step 2: Export Stock Movement Report to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $transactions = InventoryTransaction::with(['product', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->recent()
            ->get();

        $filename = "stock_movements_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date/Time', 'SKU', 'Product Name', 'Type', 'Quantity', 'Reference #', 'User', 'Notes']);

            foreach ($transactions as $tx) {
                fputcsv($handle, [
                    $tx->id,
                    $tx->created_at->format('Y-m-d H:i:s'),
                    $tx->product->sku ?? 'N/A',
                    $tx->product->name ?? 'Deleted Product',
                    $tx->type,
                    $tx->quantity,
                    $tx->reference_number ?? 'N/A',
                    $tx->user->name ?? 'System',
                    $tx->notes ?? '',
                ]);
            }
            fclose($handle);
        }, $filename);
    }
}