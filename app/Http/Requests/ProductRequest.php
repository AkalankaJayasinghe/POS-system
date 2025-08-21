<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'active' => 'boolean'
        ];

        // Modify unique rules for updates
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $product = $this->route('product');
            $rules['sku'] = 'required|string|max:100|unique:products,sku,' . $product->id;
            $rules['barcode'] = 'nullable|string|max:100|unique:products,barcode,' . $product->id;
        }

        return $rules;
    }
}
