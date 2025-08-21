@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Category: {{ $category->name }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h5>Category Details</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <tbody>
                    <tr>
                        <th style="width: 200px;">Name:</th>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <th>Slug:</th>
                        <td>{{ $category->slug }}</td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $category->description ?: 'No description provided' }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($category->active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Products Count:</th>
                        <td>{{ $category->products->count() }}</td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td>{{ $category->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $category->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between">
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Products in this category -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5>Products in this Category</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($category->products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>
                            @if($product->active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No products in this category.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
