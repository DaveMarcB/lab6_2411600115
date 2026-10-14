<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transaction Details #{{ $transaction->id }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                &larr; Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Transaction Details Card -->
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-emerald-600">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Transaction Info</h3>
                    <div class="space-y-3 text-sm">
                        <p><span class="text-gray-500 font-medium">Type:</span>
                            <span class="uppercase font-bold ml-1">{{ str_replace('_', ' ', $transaction->type) }}</span>
                        </p>
                        <p><span class="text-gray-500 font-medium">Quantity Moved:</span>
                            <span class="font-bold ml-1 {{ $transaction->type === 'stock_out' || $transaction->quantity < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $transaction->type === 'stock_in' ? '+' . abs($transaction->quantity) : ($transaction->type === 'stock_out' ? '-' . abs($transaction->quantity) : $transaction->quantity) }}
                            </span>
                        </p>
                        <p><span class="text-gray-500 font-medium">Reference #:</span> {{ $transaction->reference_number ?? 'N/A' }}</p>
                        <p><span class="text-gray-500 font-medium">Date/Time:</span> {{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                        <p><span class="text-gray-500 font-medium">Notes:</span> {{ $transaction->notes ?? 'No notes provided.' }}</p>
                    </div>
                </div>

                <!-- Product Information Card -->
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-600">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Product Information</h3>
                    @if($transaction->product)
                        <div class="space-y-3 text-sm">
                            <p><span class="text-gray-500 font-medium">Name:</span> <span class="font-semibold">{{ $transaction->product->name }}</span></p>
                            <p><span class="text-gray-500 font-medium">SKU:</span> {{ $transaction->product->sku }}</p>
                            <p><span class="text-gray-500 font-medium">Category:</span> {{ $transaction->product->category }}</p>
                            <p><span class="text-gray-500 font-medium">Current Stock:</span> <span class="font-bold">{{ $transaction->product->quantity }}</span></p>
                            <div class="pt-2">
                                <a href="{{ route('products.show', $transaction->product) }}" class="text-emerald-600 font-semibold hover:underline">
                                    View Product Page &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Product is no longer available.</p>
                    @endif
                </div>

                <!-- User Information Card -->
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-amber-500">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Authorized By</h3>
                    <div class="space-y-3 text-sm">
                        <p><span class="text-gray-500 font-medium">User Name:</span> {{ $transaction->user->name ?? 'System' }}</p>
                        <p><span class="text-gray-500 font-medium">Email:</span> {{ $transaction->user->email ?? 'N/A' }}</p>
                        <p><span class="text-gray-500 font-medium">Recorded:</span> {{ $transaction->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center">
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                    Back to Transactions
                </a>

                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Delete this transaction and reverse the stock change?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700">
                        Delete Transaction
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>