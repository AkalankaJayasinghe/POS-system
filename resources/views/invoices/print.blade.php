<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} - {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 14px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            margin: 0;
        }

        .invoice-number {
            font-size: 16px;
            margin-top: 5px;
        }

        .company-details {
            text-align: right;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
            box-sizing: border-box;
        }

        .text-right {
            text-align: right;
        }

        .invoice-details {
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .invoice-status {
            font-size: 14px;
            padding: 5px 10px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
            color: white;
            border-radius: 4px;
        }

        .status-paid {
            background-color: #28a745;
        }

        .status-unpaid {
            background-color: #dc3545;
        }

        .status-partial {
            background-color: #ffc107;
            color: #333;
        }

        .status-cancelled {
            background-color: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            margin-top: 20px;
            text-align: right;
        }

        .invoice-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        @media print {
            body {
                padding: 0;
                background-color: white;
            }

            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="row">
                <div class="col-6">
                    <h1 class="invoice-title">{{ __('INVOICE') }}</h1>
                    <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                </div>
                <div class="col-6 company-details">
                    <div class="company-name">{{ config('app.name', 'POS System') }}</div>
                    <div>123 Business Street</div>
                    <div>City, State, ZIP</div>
                    <div>Email: info@example.com</div>
                    <div>Tel: +1 234 567 890</div>
                </div>
            </div>
        </div>

        <div class="invoice-status {{ 'status-' . $invoice->status }}">
            {{ strtoupper($invoice->status) }}
        </div>

        <div class="invoice-details">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Bill To:') }}</h3>
                    <div><strong>{{ $invoice->sale->user ? $invoice->sale->user->name : 'Walk-in Customer' }}</strong>
                    </div>
                    @if($invoice->sale->user)
                    <div>{{ $invoice->sale->user->email }}</div>
                    @endif
                </div>
                <div class="col-6 text-right">
                    <div><strong>{{ __('Invoice Date:') }}</strong> {{ $invoice->invoice_date->format('d M Y') }}</div>
                    <div><strong>{{ __('Due Date:') }}</strong> {{ $invoice->due_date ? $invoice->due_date->format('d M
                        Y') : 'N/A' }}</div>
                    <div><strong>{{ __('Sale Reference:') }}</strong> {{ $invoice->sale->reference_no }}</div>
                </div>
            </div>
        </div>

        <table>
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
        </table>

        <div class="totals">
            <div><strong>{{ __('Subtotal:') }}</strong> {{ number_format($invoice->sale->saleItems->sum('subtotal'), 2)
                }}</div>
            @if($invoice->sale->discount > 0)
            <div><strong>{{ __('Discount:') }}</strong> {{ number_format($invoice->sale->discount, 2) }}</div>
            @endif
            @if($invoice->sale->tax > 0)
            <div><strong>{{ __('Tax:') }}</strong> {{ number_format($invoice->sale->tax, 2) }}</div>
            @endif
            <div><strong>{{ __('Total:') }}</strong> {{ number_format($invoice->sale->total_amount, 2) }}</div>
            <div><strong>{{ __('Paid:') }}</strong> {{ number_format($invoice->sale->paid_amount, 2) }}</div>
            @if($invoice->sale->paid_amount < $invoice->sale->total_amount)
                <div><strong>{{ __('Due:') }}</strong> {{ number_format($invoice->sale->total_amount -
                    $invoice->sale->paid_amount, 2) }}</div>
                @endif
        </div>

        @if($invoice->notes)
        <div style="margin-top: 30px;">
            <h3>{{ __('Notes:') }}</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="invoice-footer">
            <p>{{ __('Thank you for your business!') }}</p>
        </div>

        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()"
                style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">
                {{ __('Print Invoice') }}
            </button>
            <button onclick="window.close()"
                style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px; margin-left: 10px;">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</body>

</html>
