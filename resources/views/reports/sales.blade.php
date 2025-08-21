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

    .date-filter {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Sales Report') }}</span>
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
                    <!-- Date Range Filter -->
                    <div class="date-filter">
                        <form action="{{ route('reports.sales') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="date_range" class="form-label">{{ __('Date Range') }}</label>
                                <select name="date_range" id="date_range" class="form-select"
                                    onchange="this.form.submit()">
                                    <option value="today" {{ $dateRange=='today' ? 'selected' : '' }}>{{ __('Today') }}
                                    </option>
                                    <option value="yesterday" {{ $dateRange=='yesterday' ? 'selected' : '' }}>{{
                                        __('Yesterday') }}</option>
                                    <option value="this_week" {{ $dateRange=='this_week' ? 'selected' : '' }}>{{
                                        __('This Week') }}</option>
                                    <option value="this_month" {{ $dateRange=='this_month' ? 'selected' : '' }}>{{
                                        __('This Month') }}</option>
                                    <option value="this_year" {{ $dateRange=='this_year' ? 'selected' : '' }}>{{
                                        __('This Year') }}</option>
                                    <option value="custom" {{ $dateRange=='custom' ? 'selected' : '' }}>{{ __('Custom
                                        Range') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 custom-date-inputs"
                                style="{{ $dateRange == 'custom' ? '' : 'display: none;' }}">
                                <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ request('start_date', date('Y-m-d', strtotime('-30 days'))) }}">
                            </div>
                            <div class="col-md-3 custom-date-inputs"
                                style="{{ $dateRange == 'custom' ? '' : 'display: none;' }}">
                                <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ request('end_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2 custom-date-inputs"
                                style="{{ $dateRange == 'custom' ? '' : 'display: none;' }}">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">{{ __('Apply') }}</button>
                            </div>
                        </form>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card report-card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Total Sales') }}</h5>
                                    <h2 class="display-4">{{ $salesData['total_sales'] }}</h2>
                                    <p class="card-text">{{ __('Number of transactions') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card report-card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Total Revenue') }}</h5>
                                    <h2 class="display-4">${{ number_format($salesData['total_revenue'], 2) }}</h2>
                                    <p class="card-text">{{ __('Gross sales amount') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card report-card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Average Sale') }}</h5>
                                    <h2 class="display-4">${{ number_format($salesData['average_sale_value'], 2) }}</h2>
                                    <p class="card-text">{{ __('Average transaction value') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Sales Trend') }}
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Table -->
                    <div class="card">
                        <div class="card-header">
                            {{ __('Sales List') }}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Reference') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Customer') }}</th>
                                            <th>{{ __('Items') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesData['sales'] as $sale)
                                        <tr>
                                            <td>{{ $sale->reference_no }}</td>
                                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $sale->user ? $sale->user->name : 'Walk-in Customer' }}</td>
                                            <td>{{ $sale->saleItems->count() }}</td>
                                            <td>${{ number_format($sale->total_amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($sale->payment_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('No sales found for the selected
                                                period.') }}</td>
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
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle custom date range visibility
        const dateRangeSelect = document.getElementById('date_range');
        const customDateInputs = document.querySelectorAll('.custom-date-inputs');

        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateInputs.forEach(el => el.style.display = '');
            } else {
                customDateInputs.forEach(el => el.style.display = 'none');
            }
        });

        // Sales chart
        const sales = @json($salesData['sales']);
        const salesByDate = {};

        // Group sales by date
        sales.forEach(sale => {
            const date = new Date(sale.created_at).toLocaleDateString();
            if (!salesByDate[date]) {
                salesByDate[date] = {
                    count: 0,
                    total: 0
                };
            }
            salesByDate[date].count += 1;
            salesByDate[date].total += parseFloat(sale.total_amount);
        });

        // Prepare chart data
        const dates = Object.keys(salesByDate).sort((a, b) => new Date(a) - new Date(b));
        const salesCounts = dates.map(date => salesByDate[date].count);
        const salesTotals = dates.map(date => salesByDate[date].total);

        // Create chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Number of Sales',
                        data: salesCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue ($)',
                        data: salesTotals,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Sales'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Export buttons
        document.getElementById('export-pdf').addEventListener('click', function() {
            window.location.href = "{{ route('reports.export', ['type' => 'sales', 'format' => 'pdf']) }}";
        });

        document.getElementById('export-excel').addEventListener('click', function() {
            window.location.href = "{{ route('reports.export', ['type' => 'sales', 'format' => 'excel']) }}";
        });
    });
</script>
@endsection
