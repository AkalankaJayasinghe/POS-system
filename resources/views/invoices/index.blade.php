@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Invoices') }}</span>
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('New Invoice') }}
                    </a>
                </div>

                <div class="card-body">
                    @if($invoices->isEmpty())
                    <div class="alert alert-info">
                        {{ __('No invoices found.') }}
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Invoice #') }}</th>
                                    <th>{{ __('Sale Reference') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $invoice->sale) }}">
                                            {{ $invoice->sale->reference_no }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ number_format($invoice->sale->total_amount, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'partial' ? 'warning' : ($invoice->status == 'cancelled' ? 'danger' : 'info')) }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('invoices.show', $invoice) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.print', $invoice) }}"
                                                class="btn btn-secondary btn-sm" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
