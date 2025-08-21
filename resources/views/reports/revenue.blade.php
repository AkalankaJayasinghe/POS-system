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

    .period-filter {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .trend-indicator {
        font-size: 20px;
        margin-left: 10px;
    }

    .trend-up {
        color: #28a745;
    }

    .trend-down {
        color: #dc3545;
    }

    .trend-neutral {
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Revenue Report') }}</span>
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
                    <!-- Period Filter -->
                    <div class="period-filter">
                        <form action="{{ route('reports.revenue') }}" method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="period" class="form-label">{{ __('Time Period') }}</label>
                                <select name="period" id="period" class="form-select" onchange="this.form.submit()">
                                    <option value="daily" {{ $period=='daily' ? 'selected' : '' }}>{{ __('Daily (Last 30
                                        Days)') }}</option>
                                    <option value="weekly" {{ $period=='weekly' ? 'selected' : '' }}>{{ __('Weekly (Last
                                        12 Weeks)') }}</option>
                                    <option value="monthly" {{ $period=='monthly' ? 'selected' : '' }}>{{ __('Monthly
                                        (Last 12 Months)') }}</option>
                                    <option value="yearly" {{ $period=='yearly' ? 'selected' : '' }}>{{ __('Yearly (Last
                                        5 Years)') }}</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Summary Cards -->
                    @php
                    // Calculate summary data
                    $totalRevenue = array_sum(array_column($revenueData['data']->toArray(), 'revenue'));
                    $totalSales = array_sum(array_column($revenueData['data']->toArray(), 'count'));
                    $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

                    // Calculate trends (comparison with previous period)
                    $dataCount = count($revenueData['data']);
                    if ($dataCount >= 2) {
                    $currentPeriodRevenue = $revenueData['data'][$dataCount - 1]->revenue;
                    $previousPeriodRevenue = $revenueData['data'][$dataCount - 2]->revenue;
                    $revenueTrend = $previousPeriodRevenue > 0 ?
                    (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 : 0;

                    $currentPeriodSales = $revenueData['data'][$dataCount - 1]->count;
                    $previousPeriodSales = $revenueData['data'][$dataCount - 2]->count;
                    $salesTrend = $previousPeriodSales > 0 ?
                    (($currentPeriodSales - $previousPeriodSales) / $previousPeriodSales) * 100 : 0;
                    } else {
                    $revenueTrend = 0;
                    $salesTrend = 0;
                    }
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card report-card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Total Revenue') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <h2 class="display-4">${{ number_format($totalRevenue, 2) }}</h2>
                                        @if(isset($revenueTrend))
                                        <span
                                            class="trend-indicator {{ $revenueTrend > 0 ? 'trend-up' : ($revenueTrend < 0 ? 'trend-down' : 'trend-neutral') }}">
                                            @if($revenueTrend > 0)
                                            <i class="fas fa-arrow-up"></i> {{ number_format(abs($revenueTrend), 1) }}%
                                            @elseif($revenueTrend < 0) <i class="fas fa-arrow-down"></i> {{
                                                number_format(abs($revenueTrend), 1) }}%
                                                @else
                                                <i class="fas fa-equals"></i> 0%
                                                @endif
                                        </span>
                                        @endif
                                    </div>
                                    <p class="card-text">{{ __('Total revenue for selected period') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card report-card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Total Sales') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <h2 class="display-4">{{ number_format($totalSales) }}</h2>
                                        @if(isset($salesTrend))
                                        <span
                                            class="trend-indicator {{ $salesTrend > 0 ? 'trend-up' : ($salesTrend < 0 ? 'trend-down' : 'trend-neutral') }}">
                                            @if($salesTrend > 0)
                                            <i class="fas fa-arrow-up"></i> {{ number_format(abs($salesTrend), 1) }}%
                                            @elseif($salesTrend < 0) <i class="fas fa-arrow-down"></i> {{
                                                number_format(abs($salesTrend), 1) }}%
                                                @else
                                                <i class="fas fa-equals"></i> 0%
                                                @endif
                                        </span>
                                        @endif
                                    </div>
                                    <p class="card-text">{{ __('Number of transactions') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card report-card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Average Sale') }}</h5>
                                    <h2 class="display-4">${{ number_format($averageSale, 2) }}</h2>
                                    <p class="card-text">{{ __('Average transaction value') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Revenue Trend') }}
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Table -->
                    <div class="card">
                        <div class="card-header">
                            {{ __('Revenue Breakdown') }}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Period') }}</th>
                                            <th>{{ __('Sales Count') }}</th>
                                            <th>{{ __('Revenue') }}</th>
                                            <th>{{ __('Average Sale') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($revenueData['data'] as $data)
                                        <tr>
                                            <td>{{ $data->period }}</td>
                                            <td>{{ $data->count }}</td>
                                            <td>${{ number_format($data->revenue, 2) }}</td>
                                            <td>${{ $data->count > 0 ? number_format($data->revenue / $data->count, 2) :
                                                '0.00' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-dark">
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ number_format($totalSales) }}</th>
                                            <th>${{ number_format($totalRevenue, 2) }}</th>
                                            <th>${{ number_format($averageSale, 2) }}</th>
                                        </tr>
                                    </tfoot>
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
        // Revenue Chart
        const revenueData = @json($revenueData['data']);
        const periods = revenueData.map(data => data.period);
        const revenues = revenueData.map(data => data.revenue);
        const salesCounts = revenueData.map(data => data.count);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: periods,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        data: revenues,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Sales Count',
                        data: salesCounts,
                        type: 'line',
                        fill: false,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        pointRadius: 3,
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
                    x: {
                        title: {
                            display: true,
                            text: 'Period'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Sales Count'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Revenue and Sales Trend'
                    }
                }
            }
        });

        // Export buttons
        document.getElementById('export-pdf').addEventListener('click', function() {
            window.location.href = "{{ route('reports.export', ['type' => 'revenue', 'format' => 'pdf']) }}";
        });

        document.getElementById('export-excel').addEventListener('click', function() {
            window.location.href = "{{ route('reports.export', ['type' => 'revenue', 'format' => 'excel']) }}";
        });
    });
</script>
@endsection
