<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record Inventory Transaction') }}
        </h2>
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

                <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
                    @csrf

                    <!-- Product Selection -->
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Select Product *</label>
                        <select id="product_id" name="product_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="" data-stock="0">-- Choose a Product --</option>
                            <?php foreach ($products as$product): ?>
                                <option value="{{ $product->id }}" data-stock="{{ $product->quantity }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->sku }} - {{ $product->name }} (Current Stock: {{$product->quantity }})
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="current-stock-display" class="mt-2 text-sm font-semibold text-emerald-700 hidden">
                            Available Stock: <span id="stock-count">0</span> units
                        </p>
                    </div>

                    <!-- Transaction Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <?php foreach ($transactionTypes as $value =>$label): ?>
                                <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="type" value="{{ $value }}" class="text-emerald-600 focus:ring-emerald-500 transaction-type-radio"
                                        {{ old('type', 'stock_in') === $value ? 'checked' : '' }} required>
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <p id="quantity-help" class="mt-1 text-xs text-gray-500">Enter the number of units moved.</p>
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number (PO#, SO#, Invoice#)</label>
                        <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" maxlength="100"
                            placeholder="e.g., PO-2026-001"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes / Reason</label>
                        <textarea id="notes" name="notes" rows="3" maxlength="1000"
                            placeholder="Additional details regarding this stock movement..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                            Save Transaction
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('product_id');
            const stockDisplay = document.getElementById('current-stock-display');
            const stockCount = document.getElementById('stock-count');
            const quantityInput = document.getElementById('quantity');
            const quantityHelp = document.getElementById('quantity-help');
            const typeRadios = document.querySelectorAll('.transaction-type-radio');

            function updateStockConstraints() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const currentStock = parseInt(selectedOption.getAttribute('data-stock') || '0', 10);
                const selectedType = document.querySelector('.transaction-type-radio:checked')?.value;

                if (productSelect.value) {
                    stockDisplay.classList.remove('hidden');
                    stockCount.textContent = currentStock;
                } else {
                    stockDisplay.classList.add('hidden');
                }

                if (selectedType === 'stock_out') {
                    quantityInput.min = 1;
                    if (productSelect.value) {
                        quantityInput.max = currentStock;
                        quantityHelp.textContent = `Maximum allowed for Stock Out: ${currentStock} units.`;
                    }
                } else if (selectedType === 'stock_in') {
                    quantityInput.min = 1;
                    quantityInput.removeAttribute('max');
                    quantityHelp.textContent = 'Enter a positive quantity to add to stock.';
                } else {
                    quantityInput.removeAttribute('min');
                    quantityInput.removeAttribute('max');
                    quantityHelp.textContent = 'Use positive numbers to add stock or negative numbers (e.g., -5) to deduct stock.';
                }
            }

            productSelect.addEventListener('change', updateStockConstraints);
            typeRadios.forEach(radio => radio.addEventListener('change', updateStockConstraints));

            updateStockConstraints();
        });
    </script>
</x-app-layout>