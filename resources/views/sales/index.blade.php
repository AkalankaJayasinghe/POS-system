@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Sales') }}</span>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('New Sale') }}
                    </a>
                </div>

                <div class="card-body">
                    @if($sales->isEmpty())
                    <div class="alert alert-info">
                        {{ __('No sales found.') }}
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Reference') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                <tr>
                                    <td>{{ $sale->reference_no }}</td>
                                    <td>{{ $sale->created_at->format('d M Y') }}</td>
                                    <td>{{ $sale->user ? $sale->user->name : 'Walk-in Customer' }}</td>
                                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'info' : 'warning') }}">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $sale->payment_status == 'paid' ? 'success' : ($sale->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$sale->invoice)
                                            <a href="{{ route('invoices.create', ['sale_id' => $sale->id]) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-file-invoice"></i> {{ __('Create Invoice') }}
                                            </a>
                                            @endif
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
