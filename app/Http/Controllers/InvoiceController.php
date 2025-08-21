<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Sale;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        $invoices = Invoice::with('sale')->latest()->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $sales = Sale::doesntHave('invoice')->get();
        return view('invoices.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string'
        ]);

        // Generate invoice number
        $validated['invoice_number'] = $this->invoiceService->generateInvoiceNumber();

        $invoice = Invoice::create($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('sale.saleItems.product', 'sale.user');
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('sale.saleItems.product', 'sale.user');
        return view('invoices.print', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:paid,unpaid,partial,cancelled',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully');
    }
}
