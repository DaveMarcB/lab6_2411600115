<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hardware Dashboard</h2>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Manage Inventory</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-primary border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Total Products</h5>
                            <h2 class="text-primary">{{ $totalProducts }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-warning border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Low Stock</h5>
                            <h2 class="text-warning">{{ $lowStock }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-danger border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Out of Stock</h5>
                            <h2 class="text-danger">{{ $outOfStock }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mb-4 shadow-sm border-0 border-start border-success border-5">
                        <div class="card-body">
                            <h5 class="text-muted">Inventory Value</h5>
                            <h2 class="text-success">₱{{ number_format($inventoryValue, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>