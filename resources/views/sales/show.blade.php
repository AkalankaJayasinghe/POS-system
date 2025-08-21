@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Sale Details') }}</span>
                    <div>
                        @if(!$sale->invoice)
                        <a href="{{ route('invoices.create', ['sale_id' => $sale->id]) }}"
                            class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-file-invoice"></i> {{ __('Create Invoice') }}
                        </a>
                        @else
                        <a href="{{ route('invoices.show', $sale->invoice) }}" class="btn btn-info btn-sm me-2">
                            <i class="fas fa-file-invoice"></i> {{ __('View Invoice') }}
                        </a>
                        @endif
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to Sales') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('Sale Information') }}</h5>
                            <p><strong>{{ __('Reference No:') }}</strong> {{ $sale->reference_no }}</p>
                            <p><strong>{{ __('Date:') }}</strong> {{ $sale->created_at->format('d M Y H:i') }}</p>
                            <p><strong>{{ __('Customer:') }}</strong> {{ $sale->user ? $sale->user->name : 'Walk-in
                                Customer' }}</p>
                            <p><strong>{{ __('Note:') }}</strong> {{ $sale->note ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 text-md-end">
                            <h5>{{ __('Payment Information') }}</h5>
                            <p>
                                <strong>{{ __('Payment Status:') }}</strong>
                                <span
                                    class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </p>
                            <p>
                                <strong>{{ __('Payment Method:') }}</strong>
                                <span
                                    class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'info' : 'warning') }}">
                                    {{ ucfirst($sale->payment_method) }}
                                </span>
                            </p>
                            <p><strong>{{ __('Total Amount:') }}</strong> {{ number_format($sale->total_amount, 2) }}
                            </p>
                            <p><strong>{{ __('Paid Amount:') }}</strong> {{ number_format($sale->paid_amount, 2) }}</p>
                        </div>
                    </div>

                    <h5 class="mt-4">{{ __('Products') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Unit Price') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->saleItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->discount, 2) }}</td>
                                    <td>{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Subtotal:') }}</th>
                                    <th>{{ number_format($sale->saleItems->sum('subtotal'), 2) }}</th>
                                </tr>
                                @if($sale->discount > 0)
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Discount:') }}</th>
                                    <th>{{ number_format($sale->discount, 2) }}</th>
                                </tr>
                                @endif
                                @if($sale->tax > 0)
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Tax:') }}</th>
                                    <th>{{ number_format($sale->tax, 2) }}</th>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Total:') }}</th>
                                    <th>{{ number_format($sale->total_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Paid:') }}</th>
                                    <th>{{ number_format($sale->paid_amount, 2) }}</th>
                                </tr>
                                @if($sale->paid_amount > $sale->total_amount)
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Change:') }}</th>
                                    <th>{{ number_format($sale->paid_amount - $sale->total_amount, 2) }}</th>
                                </tr>
                                @elseif($sale->paid_amount < $sale->total_amount)
                                    <tr>
                                        <th colspan="5" class="text-end">{{ __('Due:') }}</th>
                                        <th>{{ number_format($sale->total_amount - $sale->paid_amount, 2) }}</th>
                                    </tr>
                                    @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
