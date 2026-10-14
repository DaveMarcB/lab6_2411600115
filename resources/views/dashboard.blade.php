<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">Hardware Dashboard</h2>
                <small class="text-muted">
                    Last Updated: <span id="last-updated-time" class="fw-semibold">{{ now()->format('h:i:s A') }}</span>
                    <span id="polling-status" class="ms-2 badge bg-success" style="font-size: 0.7rem;">Live (30s)</span>
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('transactions.create') }}" class="btn btn-outline-success">+ Record Transaction</a>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Manage Inventory</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-primary border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Total Products</h5>
                            <h2 id="stat-total-products" class="text-primary transition-all">{{ $totalProducts }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-warning border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Low Stock</h5>
                            <h2 id="stat-low-stock" class="text-warning transition-all">{{ $lowStock }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-danger border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Out of Stock</h5>
                            <h2 id="stat-out-of-stock" class="text-danger transition-all">{{ $outOfStock }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-success border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Inventory Value</h5>
                            <h2 id="stat-inventory-value" class="text-success transition-all">₱{{ number_format($inventoryValue, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts & Recent Transactions -->
            <div class="row mt-2">
                <!-- Low Stock Alerts Section -->
                <div class="col-lg-5 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fw-bold text-danger">Low Stock Alerts</h5>
                            <a href="{{ route('products.index') }}" class="text-decoration-none small">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Reorder</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="low-stock-table-body">
                                        @forelse($lowStockProducts as $item)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('products.show', $item->id) }}" class="fw-semibold text-decoration-none text-dark">
                                                        {{ $item->name }}
                                                    </a>
                                                    <div class="small text-muted">{{ $item->sku }}</div>
                                                </td>
                                                <td class="fw-bold">{{ $item->quantity }}</td>
                                                <td class="text-muted">{{ $item->reorder_level }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->stock_status_color }}">
                                                        {{ $item->stock_status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    All products are well-stocked.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions Table -->
                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fw-bold text-dark">Recent Transactions</h5>
                            <a href="{{ route('transactions.index') }}" class="text-decoration-none small">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Product</th>
                                            <th>Type</th>
                                            <th>Qty</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-transactions-table-body">
                                        @forelse($recentTransactions as $tx)
                                            <tr>
                                                <td class="small text-muted">{{ $tx->created_at->format('M d, h:i A') }}</td>
                                                <td>
                                                    @if($tx->product)
                                                        <a href="{{ route('products.show', $tx->product_id) }}" class="text-decoration-none fw-semibold text-dark">
                                                            {{ $tx->product->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Deleted</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tx->type === 'stock_in')
                                                        <span class="badge bg-success">Stock In</span>
                                                    @elseif($tx->type === 'stock_out')
                                                        <span class="badge bg-danger">Stock Out</span>
                                                    @else
                                                        <span class="badge bg-info text-dark">Adjustment</span>
                                                    @endif
                                                </td>
                                                <td class="fw-bold {{ $tx->type === 'stock_out' || ($tx->type === 'adjustment' && $tx->quantity < 0) ? 'text-danger' : 'text-success' }}">
                                                    {{ $tx->type === 'stock_in' ? '+' . abs($tx->quantity) : ($tx->type === 'stock_out' ? '-' . abs($tx->quantity) : ($tx->quantity >= 0 ? '+' . $tx->quantity : $tx->quantity)) }}
                                                </td>
                                                <td class="small text-muted">{{ $tx->user->name ?? 'System' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No recent transactions recorded.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Part 7 Step 3: Load AJAX Polling Script -->
    <script src="{{ asset('js/dashboard-polling.js') }}"></script>
</x-app-layout>