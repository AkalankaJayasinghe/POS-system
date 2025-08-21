<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
            ],
            [
                'name' => 'Clothing',
                'description' => 'Shirts, pants, and other clothing items',
            ],
            [
                'name' => 'Groceries',
                'description' => 'Food items and household supplies',
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Items for home and kitchen',
            ],
            [
                'name' => 'Books',
                'description' => 'Books and publications',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'active' => true,
            ]);
        }
    }
}
