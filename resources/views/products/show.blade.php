<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Product Details & History: {{ $product->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-slate-600 text-white rounded-md text-sm font-semibold hover:bg-slate-700">
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                    &larr; Back to Products
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-lg shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 border-l-4 border-emerald-600">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">SKU & Category</p>
                    <p class="text-lg font-bold text-gray-800">{{ $product->sku }}</p>
                    <p class="text-sm text-gray-600">{{ $product->category }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Current Stock</p>
                    <p class="text-2xl font-bold text-emerald-700">{{ $product->quantity }}</p>
                    <p class="text-xs text-gray-500">Reorder Level: {{ $product->reorder_level }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Unit Price & Total Value</p>
                    <p class="text-lg font-bold text-gray-800">₱{{ number_format($product->unit_price, 2) }}</p>
                    <p class="text-xs text-gray-500">Total: ₱{{ number_format($product->quantity * $product->unit_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Stock Status</p>
                    <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full
                        {{ $product->isOutOfStock() ? 'bg-red-100 text-red-800' : ($product->isLowStock() ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800') }}">
                        {{ $product->stock_status }}
                    </span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Product Transaction History</h3>
                        <p class="text-xs text-gray-500">Audit trail showing stock movements and running balance after each transaction.</p>
                    </div>
                    <form method="GET" action="{{ route('products.show', $product) }}" class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                            Filter
                        </button>
                        <a href="{{ route('products.show', $product) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 border-b-2 border-emerald-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date/Time</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Qty Change</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Running Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Reference #</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Authorized By</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($transactions->count() > 0): ?>
                                <?php foreach ($transactions as $tx): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $tx->type === 'stock_in' ? 'bg-green-100 text-green-800' : ($tx->type === 'stock_out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucwords(str_replace('_', ' ', $tx->type)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold {{ $tx->type === 'stock_out' || ($tx->type === 'adjustment' && $tx->quantity < 0) ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $tx->type === 'stock_in' ? '+' . abs($tx->quantity) : ($tx->type === 'stock_out' ? '-' . abs($tx->quantity) : ($tx->quantity >= 0 ? '+' . $tx->quantity : $tx->quantity)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 bg-gray-50">
                                            {{ $tx->running_balance }} units
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $tx->reference_number ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $tx->user->name ?? 'System' }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $tx->notes ?? '-' }}</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No transactions found for this product in the selected date range.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>