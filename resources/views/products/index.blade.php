<x-app-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2>Inventory List</h2>
            <a href="{{ route('products.create') }}" class="btn btn-primary">+ Add Product</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped table-hover bg-white shadow-sm rounded">
            <thead class="bg-primary text-white">
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>
                        {{ $product->name }}
                        @if($product->quantity == 0)
                            <span class="badge bg-danger">Out of Stock</span>
                        @elseif($product->quantity <= $product->reorder_level)
                            <span class="badge bg-warning text-dark">Low Stock</span>
                        @endif
                    </td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>₱{{ number_format($product->unit_price, 2) }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>