<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Product;

class BarcodeService
{
    /**
     * Generate a unique barcode
     *
     * @return string
     */
    public function generateBarcode(): string
    {
        do {
            $barcode = 'POS' . now()->format('ymd') . Str::random(6);
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Generate barcode image
     *
     * @param string $barcode
     * @return string Base64 encoded image
     */
    public function generateBarcodeImage(string $barcode): string
    {
        // In a real application, you would use a barcode generation library here
        // This is a placeholder function
        return 'data:image/png;base64,' . base64_encode($barcode);
    }
}
