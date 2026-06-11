<?php

namespace App\Modules\Product\Presentation\Requests;

use App\Modules\Product\Domain\ValueObjects\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                'unique:products,sku',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'category' => [
                'required',
                'string',
                'max:100',
            ],
            'status' => [
                'sometimes',
                Rule::in(array_column(ProductStatus::cases(), 'value')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'SKU wajib diisi',
            'sku.unique' => 'SKU sudah digunakan',
            'sku.regex' => 'SKU hanya boleh mengandung huruf kapital, angka, dan tanda hubung',
            'sku.max' => 'SKU tidak boleh lebih dari 50 karakter',
            'name.required' => 'Nama produk wajib diisi',
            'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter',
            'category.required' => 'Kategori wajib diisi',
            'category.max' => 'Kategori tidak boleh lebih dari 100 karakter',
            'status.in' => 'Status tidak valid',
        ];
    }
}