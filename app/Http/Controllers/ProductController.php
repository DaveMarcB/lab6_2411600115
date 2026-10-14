<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'category' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    /**
     * Part 9, Step 1: Display product details and transaction history with running balance.
     */
    public function show(Request $request, Product $product)
    {
        $allTransactions = $product->transactions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Calculate running balance after each transaction
        $runningQty = $product->quantity;
        foreach ($allTransactions as $tx) {
            $tx->running_balance = $runningQty;

            if ($tx->type === 'stock_in') {
                $runningQty -= abs($tx->quantity);
            } elseif ($tx->type === 'stock_out') {
                $runningQty += abs($tx->quantity);
            } else {
                $runningQty -= $tx->quantity;
            }
        }

        // Filter by date range if provided
        $transactions = $allTransactions->filter(function ($tx) use ($request) {
            if ($request->filled('start_date') && $tx->created_at->toDateString() < $request->start_date) {
                return false;
            }
            if ($request->filled('end_date') && $tx->created_at->toDateString() > $request->end_date) {
                return false;
            }
            return true;
        });

        return view('products.show', compact('product', 'transactions'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Part 8, Step 2: AJAX endpoint to load current system quantity for a product.
     */
    public function apiProductStock(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => $product->quantity,
        ]);
    }
}