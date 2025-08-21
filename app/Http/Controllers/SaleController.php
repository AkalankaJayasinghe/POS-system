<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Http\Requests\SaleRequest;
use App\Events\InventoryUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('user')->latest()->get();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->get();
        return view('sales.create', compact('products'));
    }

    public function store(SaleRequest $request)
    {
        $validated = $request->validated();

        // Generate unique reference number
        $validated['reference_no'] = 'SALE-' . Str::random(8);
        $validated['user_id'] = Auth::check() ? Auth::id() : null;

        // Create the sale
        $sale = Sale::create($validated);

        // Create the sale items
        foreach ($validated['products'] as $key => $product) {
            $productModel = Product::findOrFail($product['id']);

            // Create the sale item
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'unit_price' => $productModel->price,
                'subtotal' => $productModel->price * $product['quantity'],
                'discount' => $product['discount'] ?? 0
            ]);

            // Update inventory
            $productModel->decrement('stock_quantity', $product['quantity']);

            // Trigger inventory updated event
            event(new InventoryUpdated($productModel));
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale completed successfully');
    }

    public function show(Sale $sale)
    {
        $sale->load('saleItems.product', 'user');
        return view('sales.show', compact('sale'));
    }
}
