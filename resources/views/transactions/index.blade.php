<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Transactions') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md font-semibold text-sm hover:bg-emerald-700 transition">
                + Record Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter Form -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <select name="product_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">All Types</option>
                            <option value="stock_in" {{ request('type') == 'stock_in' ? 'selected' : '' }}>Stock In</option>
                            <option value="stock_out" {{ request('type') == 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                            <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                            Filter
                        </button>
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-b-2 border-emerald-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date/Time</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Reference #</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $transaction->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">
                                        @if($transaction->product)
                                            <a href="{{ route('products.show', $transaction->product_id) }}" class="text-emerald-600 hover:underline">
                                                {{ $transaction->product->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Deleted Product</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($transaction->type === 'stock_in')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Stock In</span>
                                        @elseif($transaction->type === 'stock_out')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Stock Out</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Adjustment</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">
                                        @if($transaction->type === 'stock_in')
                                            <span class="text-green-600">+{{ abs($transaction->quantity) }}</span>
                                        @elseif($transaction->type === 'stock_out')
                                            <span class="text-red-600">-{{ abs($transaction->quantity) }}</span>
                                        @else
                                            <span class="{{ $transaction->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->quantity >= 0 ? '+' . $transaction->quantity : $transaction->quantity }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $transaction->reference_number ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $transaction->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="px-3 py-1 bg-slate-600 text-white rounded text-xs hover:bg-slate-700">
                                            View
                                        </a>
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction? This will reverse the stock change.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No inventory transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>