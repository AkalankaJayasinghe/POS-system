<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Services\BarcodeService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barcodeService = new BarcodeService();

        // Get category IDs
        $electronicsId = Category::where('name', 'Electronics')->first()->id;
        $clothingId = Category::where('name', 'Clothing')->first()->id;
        $groceriesId = Category::where('name', 'Groceries')->first()->id;
        $homeKitchenId = Category::where('name', 'Home & Kitchen')->first()->id;
        $booksId = Category::where('name', 'Books')->first()->id;

        $products = [
            [
                'name' => 'Smartphone',
                'sku' => 'ELEC-001',
                'description' => 'Latest smartphone with advanced features',
                'price' => 699.99,
                'cost_price' => 500.00,
                'stock_quantity' => 50,
                'category_id' => $electronicsId,
            ],
            [
                'name' => 'Laptop',
                'sku' => 'ELEC-002',
                'description' => 'High-performance laptop for work and gaming',
                'price' => 1299.99,
                'cost_price' => 950.00,
                'stock_quantity' => 25,
                'category_id' => $electronicsId,
            ],
            [
                'name' => 'T-Shirt',
                'sku' => 'CLOTH-001',
                'description' => 'Cotton t-shirt available in various colors',
                'price' => 19.99,
                'cost_price' => 8.00,
                'stock_quantity' => 100,
                'category_id' => $clothingId,
            ],
            [
                'name' => 'Jeans',
                'sku' => 'CLOTH-002',
                'description' => 'Classic denim jeans',
                'price' => 49.99,
                'cost_price' => 20.00,
                'stock_quantity' => 75,
                'category_id' => $clothingId,
            ],
            [
                'name' => 'Rice (5kg)',
                'sku' => 'GROC-001',
                'description' => 'Premium basmati rice',
                'price' => 10.99,
                'cost_price' => 6.50,
                'stock_quantity' => 200,
                'category_id' => $groceriesId,
            ],
            [
                'name' => 'Cooking Oil (1L)',
                'sku' => 'GROC-002',
                'description' => 'Vegetable cooking oil',
                'price' => 5.99,
                'cost_price' => 3.20,
                'stock_quantity' => 150,
                'category_id' => $groceriesId,
            ],
            [
                'name' => 'Coffee Maker',
                'sku' => 'HOME-001',
                'description' => 'Automatic drip coffee maker',
                'price' => 89.99,
                'cost_price' => 45.00,
                'stock_quantity' => 30,
                'category_id' => $homeKitchenId,
            ],
            [
                'name' => 'Cutting Board',
                'sku' => 'HOME-002',
                'description' => 'Wooden cutting board',
                'price' => 24.99,
                'cost_price' => 10.00,
                'stock_quantity' => 60,
                'category_id' => $homeKitchenId,
            ],
            [
                'name' => 'Novel',
                'sku' => 'BOOK-001',
                'description' => 'Bestselling fiction novel',
                'price' => 14.99,
                'cost_price' => 7.50,
                'stock_quantity' => 100,
                'category_id' => $booksId,
            ],
            [
                'name' => 'Cookbook',
                'sku' => 'BOOK-002',
                'description' => 'Collection of recipes',
                'price' => 19.99,
                'cost_price' => 9.00,
                'stock_quantity' => 45,
                'category_id' => $booksId,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'barcode' => $barcodeService->generateBarcode(),
                'description' => $product['description'],
                'price' => $product['price'],
                'cost_price' => $product['cost_price'],
                'stock_quantity' => $product['stock_quantity'],
                'alert_quantity' => 10,
                'category_id' => $product['category_id'],
                'active' => true,
            ]);
        }
    }
}
