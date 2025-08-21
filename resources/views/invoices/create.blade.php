@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Create Invoice') }}</span>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Invoices') }}
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('invoices.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="sale_id" class="form-label">{{ __('Select Sale') }}</label>
                            <select class="form-select @error('sale_id') is-invalid @enderror" id="sale_id"
                                name="sale_id" required {{ request('sale_id') ? 'disabled' : '' }}>
                                <option value="">{{ __('Select Sale') }}</option>
                                @foreach($sales as $sale)
                                <option value="{{ $sale->id }}" {{ (old('sale_id')==$sale->id || request('sale_id') ==
                                    $sale->id) ? 'selected' : '' }}>
                                    {{ $sale->reference_no }} - {{ number_format($sale->total_amount, 2) }} - {{
                                    $sale->created_at->format('d M Y') }}
                                </option>
                                @endforeach
                            </select>
                            @if(request('sale_id'))
                            <input type="hidden" name="sale_id" value="{{ request('sale_id') }}">
                            @endif
                            @error('sale_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="invoice_date" class="form-label">{{ __('Invoice Date') }}</label>
                            <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}"
                                required>
                            @error('invoice_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">{{ __('Due Date') }}</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                id="due_date" name="due_date"
                                value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}">
                            @error('due_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Create Invoice') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
