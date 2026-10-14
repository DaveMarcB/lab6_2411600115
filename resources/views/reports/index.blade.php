<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Reports & Valuation') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                    Export Movements CSV
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-slate-700 text-white rounded-md text-sm font-semibold hover:bg-slate-800">
                    Print / Save PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                        Generate Report
                    </button>
                </form>
            </div>

            <!-- 1. Stock Movement Report -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">1. Stock Movement Report ({{ $startDate }} to {{ $endDate }})</h3>
                    <p class="text-xs text-gray-500">Total Movements Recorded: {{ $stockMovements->count() }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date/Time</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Reference #</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($stockMovements->count() > 0): ?>
                                <?php foreach ($stockMovements as $tx): ?>
                                    <tr>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap font-medium">{{ $tx->product->name ?? 'Deleted' }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap uppercase text-xs font-bold">{{ str_replace('_', ' ', $tx->type) }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap font-bold">{{ $tx->quantity }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $tx->reference_number ?? 'N/A' }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $tx->user->name ?? 'System' }}</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-gray-500">No stock movements in this date range.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Low Stock Report -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-red-700">2. Low Stock Report (At or Below Reorder Level)</h3>
                    <p class="text-xs text-gray-500">Items Requiring Immediate Replenishment: {{ $lowStockProducts->count() }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Current Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Reorder Level</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($lowStockProducts->count() > 0): ?>
                                <?php foreach ($lowStockProducts as $product): ?>
                                    <tr>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $product->sku }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap font-semibold">{{ $product->name }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap font-bold text-red-600">{{ $product->quantity }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $product->reorder_level }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap font-bold">{{ $product->stock_status }}</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">All products are above reorder levels.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Inventory Valuation Report -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">3. Inventory Valuation Report</h3>
                        <p class="text-xs text-gray-500">Current asset valuation across all hardware items</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 uppercase font-bold block">Total Inventory Value</span>
                        <span class="text-2xl font-bold text-emerald-700">₱{{ number_format($totalValuation, 2) }}</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Total Asset Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($allProducts as $product): ?>
                                <tr>
                                    <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $product->sku }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap font-medium">{{ $product->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $product->category }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap font-bold">{{ $product->quantity }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-gray-600">₱{{ number_format($product->unit_price, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap font-bold text-emerald-700">
                                        ₱{{ number_format($product->quantity * $product->unit_price, 2) }}
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>