<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Generate a unique invoice number
     *
     * @return string
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastInvoice = Invoice::latest()->first();
        
        if ($lastInvoice) {
            $lastNumber = substr($lastInvoice->invoice_number, -4);
            $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        
        return $prefix . $date . $nextNumber;
    }
    
    /**
     * Create an invoice for a sale
     *
     * @param Sale $sale
     * @return Invoice
     */
    public function createInvoice(Sale $sale): Invoice
    {
        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'sale_id' => $sale->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending'
        ]);
    }
}
