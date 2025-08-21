@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<style>
    .report-card {
        transition: all 0.3s;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stock-warning {
        color: #f0ad4e;
    }

    .stock-danger {
        color: #d9534f;
    }

    .stock-success {
        color: #5cb85c;
    }

    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Inventory Report') }}</span>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" id="export-pdf">
                            <i class="fas fa-file-pdf"></i> {{ __('Export PDF') }}
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="export-excel">
                            <i class="fas fa-file-excel"></i> {{ __('Export Excel') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card report-card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Total Products') }}</h5>
                                    <h2 class="display-4">{{ $products->count() }}</h2>
                                    <p class="card-text">{{ __('All products in inventory') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card report-card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('In Stock') }}</h5>
                                    <h2 class="display-4">{{ $products->where('stock_quantity', '>', 0)->count() }}</h2>
                                    <p class="card-text">{{ __('Products available for sale') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card report-card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Low Stock') }}</h5>
                                    <h2 class="display-4">{{ $lowStockProducts->count() }}</h2>
                                    <p class="card-text">{{ __('Products below alert quantity') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card report-card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Out of Stock') }}</h5>
                                    <h2 class="display-4">{{ $products->where('stock_quantity', '=', 0)->count() }}</h2>
                                    <p class="card-text">{{ __('Products needing restock') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Chart -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Stock by Category') }}
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Top 10 Products by Value') }}
                                </div>
                                <div class="card-body">
                                    <canvas id="valueChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    @if($lowStockProducts->count() > 0)
                    <div class="alert alert-warning mb-4">
                        <h5><i class="fas fa-exclamation-triangle"></i> {{ __('Low Stock Alert') }}</h5>
                        <p>{{ __('The following products are below their alert quantity and need to be restocked:') }}
                        </p>
                        <ul>
                            @foreach($lowStockProducts as $product)
                            <li>
                                <strong>{{ $product->name }}</strong>:
                                {{ $product->stock_quantity }} {{ __('left') }}
                                ({{ __('Alert level:') }} {{ $product->alert_quantity }})
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Inventory Table -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>{{ __('Product Inventory') }}</span>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" id="inventory-search" class="form-control"
                                    placeholder="Search products...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="inventory-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Image') }}</th>
                                            <th>{{ __('SKU') }}</th>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Category') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Cost') }}</th>
                                            <th>{{ __('Stock') }}</th>
                                            <th>{{ __('Value') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" class="product-image">
                                                @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="fas fa-box text-secondary"></i>
                                                </div>
                                                @endif
                                            </td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>${{ number_format($product->cost_price, 2) }}</td>
                                            <td>
                                                @if($product->stock_quantity <= 0) <span class="stock-danger">{{
                                                    $product->stock_quantity }}</span>
                                                    @elseif($product->stock_quantity <= $product->alert_quantity)
                                                        <span class="stock-warning">{{ $product->stock_quantity
                                                            }}</span>
                                                        @else
                                                        <span class="stock-success">{{ $product->stock_quantity
                                                            }}</span>
                                                        @endif
                                            </td>
                                            <td>${{ number_format($product->stock_quantity * $product->cost_price, 2) }}
                                            </td>
                                            <td>
                                                @if($product->stock_quantity <= 0) <span class="badge bg-danger">{{
                                                    __('Out of Stock') }}</span>
                                                    @elseif($product->stock_quantity <= $product->alert_quantity)
                                                        <span class="badge bg-warning text-dark">{{ __('Low Stock')
                                                            }}</span>
                                                        @else
                                                        <span class="badge bg-success">{{ __('In Stock') }}</span>
                                                        @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inventory search functionality
        const inventorySearch = document.getElementById('inventory-search');
        const inventoryTable = document.getElementById('inventory-table');

        if (inventorySearch) {
            inventorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = inventoryTable.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Category Chart
        const products = @json($products);
        const categoryCounts = {};

        products.forEach(product => {
            const categoryName = product.category ? product.category.name : 'Uncategorized';
            if (!categoryCounts[categoryName]) {
                categoryCounts[categoryName] = {
                    count: 0,
                    value: 0
                };
            }
            categoryCounts[categoryName].count += 1;
            categoryCounts[categoryName].value += product.stock_quantity * product.cost_price;
        });

        const categoryNames = Object.keys(categoryCounts);
        const categoryCommunity = categoryNames.map(cat => categoryCounts[cat].count);

        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'pie',
            data: {
                labels: categoryNames,
                datasets: [{
                    data: categoryCommunity,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(40, 159, 64, 0.7)',
                        'rgba(210, 199, 199, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Products by Category'
                    }
                }
            }
        });

        // Value Chart
        const productsByValue = [...products]
            .map(p => ({
                name: p.name,
                value: p.stock_quantity * p.cost_price
            }))
            .sort((a, b) => b.value - a.value)
            .slice(0, 10);

        const ctxValue = document.getElementById('valueChart').getContext('2d');
        new Chart(ctxValue, {
            type: 'bar',
            data: {
                labels: productsByValue.map(p => p.name),
                datasets: [{
                    label: 'Inventory Value ($)',
                    data: productsByValue.map(p => p.value),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Top 10 Products by Inventory Value'
                    }
                }
            }
        });

        // Export buttons
        document.getElementById('export-pdf').addEventListener('click', function() {
            window.location.href = "{{ route('reports.export', ['type' => 'inventory', 'format' => 'pdf']) }}";
        });

        document.getElementById('export-excel').addEventListener('click', function() {
            window.location.href = "{{ route('reports.export', ['type' => 'inventory', 'format' => 'excel']) }}";
        });
    });
</script>
@endsection
