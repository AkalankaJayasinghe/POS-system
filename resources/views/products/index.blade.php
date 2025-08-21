@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Products</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h5>Product List</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" width="50"
                                height="50" class="img-thumbnail">
                            @else
                            <img src="{{ asset('images/no-image.png') }}" alt="No Image" width="50" height="50"
                                class="img-thumbnail">
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>
                            @if($product->stock_quantity <= $product->alert_quantity)
                                <span class="text-danger fw-bold">{{ $product->stock_quantity }}</span>
                                @else
                                <span>{{ $product->stock_quantity }}</span>
                                @endif
                        </td>
                        <td>
                            @if($product->active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
