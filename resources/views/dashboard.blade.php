@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Dashboard</h2>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Sales</h5>
                    <h2 class="mb-0">{{ $todaySales }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h2 class="mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <h2 class="mb-0">{{ $totalProducts }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Products</h5>
                    <h2 class="mb-0">{{ $lowStockProducts }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales & Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recent Sales</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}">{{ $sale->reference_no }}</a>
                                </td>
                                <td>${{ number_format($sale->total_amount, 2) }}</td>
                                <td>{{ $sale->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No sales found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">View All Sales</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Weekly Sales Chart</div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('sales.create') }}" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-shopping-cart"></i> New Sale
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('products.create') }}" class="btn btn-success btn-block w-100">
                                <i class="fas fa-box"></i> Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('categories.create') }}" class="btn btn-info btn-block w-100">
                                <i class="fas fa-tags"></i> Add Category
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('invoices.create') }}" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-file-invoice"></i> Create Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Reports</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-chart-line"></i> Sales Report
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('reports.inventory') }}" class="btn btn-success btn-block w-100">
                                <i class="fas fa-boxes"></i> Inventory Report
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('reports.revenue') }}" class="btn btn-info btn-block w-100">
                                <i class="fas fa-dollar-sign"></i> Revenue Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Prepare data from salesData collection
        const labels = @json($salesData->pluck('date'));
        const data = @json($salesData->pluck('total'));

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales Amount ($)',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
