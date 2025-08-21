<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductRequest;
use App\Services\BarcodeService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    public function index()
    {
        $products = Product::with('category')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        // Generate barcode if not provided
        if (empty($validated['barcode'])) {
            $validated['barcode'] = $this->barcodeService->generateBarcode();
        }
        
        Product::create($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $product->update($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
