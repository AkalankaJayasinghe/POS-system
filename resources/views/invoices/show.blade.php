@extends('layouts.app')

@section('styles')
<style>
    .invoice-header {
        padding: 1.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }

    .invoice-footer {
        padding: 1.5rem 0;
        border-top: 1px solid #dee2e6;
    }

    .invoice-title {
        font-size: 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #333;
    }

    .invoice-details {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .invoice-status {
        font-size: 1.2rem;
        padding: 0.5rem 1rem;
        display: inline-block;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Invoice Details') }}</span>
                    <div>
                        <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-secondary btn-sm me-2"
                            target="_blank">
                            <i class="fas fa-print"></i> {{ __('Print Invoice') }}
                        </a>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to Invoices') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="invoice-header">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="invoice-title">{{ __('INVOICE') }}</div>
                                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="company-name">{{ config('app.name', 'POS System') }}</div>
                                <div class="company-address">123 Business Street</div>
                                <div class="company-contact">Email: info@example.com | Tel: +1 234 567 890</div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="invoice-status bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'partial' ? 'warning' : ($invoice->status == 'cancelled' ? 'danger' : 'info')) }} text-white">
                        {{ strtoupper($invoice->status) }}
                    </div>

                    <div class="invoice-details">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ __('Bill To:') }}</h5>
                                <p>{{ $invoice->sale->user ? $invoice->sale->user->name : 'Walk-in Customer' }}</p>
                                @if($invoice->sale->user)
                                <p>{{ $invoice->sale->user->email }}</p>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p><strong>{{ __('Invoice Date:') }}</strong> {{ $invoice->invoice_date->format('d M Y')
                                    }}</p>
                                <p><strong>{{ __('Due Date:') }}</strong> {{ $invoice->due_date ?
                                    $invoice->due_date->format('d M Y') : 'N/A' }}</p>
                                <p><strong>{{ __('Sale Reference:') }}</strong> {{ $invoice->sale->reference_no }}</p>
                            </div>
                        </div>
                    </div>

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
                                @foreach($invoice->sale->saleItems as $index => $item)
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
                                    <th>{{ number_format($invoice->sale->saleItems->sum('subtotal'), 2) }}</th>
                                </tr>
                                @if($invoice->sale->discount > 0)
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Discount:') }}</th>
                                    <th>{{ number_format($invoice->sale->discount, 2) }}</th>
                                </tr>
                                @endif
                                @if($invoice->sale->tax > 0)
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Tax:') }}</th>
                                    <th>{{ number_format($invoice->sale->tax, 2) }}</th>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Total:') }}</th>
                                    <th>{{ number_format($invoice->sale->total_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">{{ __('Paid:') }}</th>
                                    <th>{{ number_format($invoice->sale->paid_amount, 2) }}</th>
                                </tr>
                                @if($invoice->sale->paid_amount < $invoice->sale->total_amount)
                                    <tr>
                                        <th colspan="5" class="text-end">{{ __('Due:') }}</th>
                                        <th>{{ number_format($invoice->sale->total_amount - $invoice->sale->paid_amount,
                                            2) }}</th>
                                    </tr>
                                    @endif
                            </tfoot>
                        </table>
                    </div>

                    @if($invoice->notes)
                    <div class="mt-4">
                        <h5>{{ __('Notes:') }}</h5>
                        <p>{{ $invoice->notes }}</p>
                    </div>
                    @endif

                    <div class="invoice-footer mt-5">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p>{{ __('Thank you for your business!') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
