<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stock Adjustment (Physical Count Reconciliation)') }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                &larr; Back to Transactions
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">

                <?php if ($errors->any()): ?>
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        <ul class="list-disc pl-5 text-sm">
                            <?php foreach ($errors->all() as$error): ?>
                                <li>{{ $error }}</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="{{ route('adjustments.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Select Product *</label>
                        <select id="product_id" name="product_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Choose a Product to Adjust --</option>
                            <?php foreach ($products as$product): ?>
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->sku }} - {{$product->name }}
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="system_quantity" class="block text-sm font-medium text-gray-700 mb-1">Current System Qty (AJAX)</label>
                            <input type="text" id="system_quantity" readonly value="-"
                                class="w-full rounded-md bg-gray-100 border-gray-300 text-gray-700 font-bold shadow-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label for="physical_count" class="block text-sm font-medium text-gray-700 mb-1">Physical Count *</label>
                            <input type="number" id="physical_count" name="physical_count" value="{{ old('physical_count') }}" min="0" required
                                placeholder="Enter actual count"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label for="calculated_difference" class="block text-sm font-medium text-gray-700 mb-1">Calculated Difference</label>
                            <input type="text" id="calculated_difference" readonly value="0"
                                class="w-full rounded-md bg-gray-100 border-gray-300 font-bold shadow-sm cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Adjustment *</label>
                        <select id="reason" name="reason" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">-- Select a Reason --</option>
                            <?php foreach ($reasons as $value =>$label): ?>
                                <option value="{{ $value }}" {{ old('reason') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3" maxlength="1000"
                            placeholder="Explain the audit findings or damage details..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                            Apply Adjustment
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('product_id');
            const systemQtyInput = document.getElementById('system_quantity');
            const physicalInput = document.getElementById('physical_count');
            const diffInput = document.getElementById('calculated_difference');
            let currentSystemQty = null;

            function calculateDifference() {
                if (currentSystemQty === null || physicalInput.value === '') {
                    diffInput.value = '-';
                    diffInput.className = 'w-full rounded-md bg-gray-100 border-gray-300 font-bold shadow-sm cursor-not-allowed';
                    return;
                }
                const diff = parseInt(physicalInput.value, 10) - currentSystemQty;
                diffInput.value = diff > 0 ? `+${diff}` : String(diff);
                diffInput.className = 'w-full rounded-md bg-gray-100 border-gray-300 font-bold shadow-sm cursor-not-allowed ' +
                    (diff > 0 ? 'text-green-600' : (diff < 0 ? 'text-red-600' : 'text-gray-700'));
            }

            async function fetchProductStock(productId) {
                if (!productId) {
                    currentSystemQty = null;
                    systemQtyInput.value = '-';
                    calculateDifference();
                    return;
                }
                systemQtyInput.value = 'Loading...';
                try {
                    const response = await fetch(`/api/products/${productId}/stock`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    currentSystemQty = parseInt(data.quantity, 10);
                    systemQtyInput.value = currentSystemQty;
                    calculateDifference();
                } catch (e) {
                    systemQtyInput.value = 'Error';
                }
            }

            productSelect.addEventListener('change', () => fetchProductStock(productSelect.value));
            physicalInput.addEventListener('input', calculateDifference);

            if (productSelect.value) {
                fetchProductStock(productSelect.value);
            }
        });
    </script>
</x-app-layout>