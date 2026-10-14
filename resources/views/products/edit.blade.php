<x-app-layout>
    <div class="container py-5">
        <div class="card shadow-sm border-0 border-start border-primary border-5">
            <div class="card-header bg-white">
                <h3 class="mb-0 text-primary">Edit Product</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control" required>
                                <option value="Lumber" {{ old('category', $product->category) == 'Lumber' ? 'selected' : '' }}>Lumber</option>
                                <option value="Hardware" {{ old('category', $product->category) == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="Tools" {{ old('category', $product->category) == 'Tools' ? 'selected' : '' }}>Tools</option>
                                <option value="Electrical" {{ old('category', $product->category) == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                <option value="Plumbing" {{ old('category', $product->category) == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                                <option value="Paint" {{ old('category', $product->category) == 'Paint' ? 'selected' : '' }}>Paint</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier (Optional)</label>
                            <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $product->supplier) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit Price (₱)</label>
                            <input type="number" name="unit_price" class="form-control" step="0.01" value="{{ old('unit_price', $product->unit_price) }}" min="0" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>