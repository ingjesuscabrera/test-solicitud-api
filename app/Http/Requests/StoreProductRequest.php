<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['nullable', 'numeric', 'min:0'],
            'manufacturing_cost' => ['nullable', 'numeric', 'min:0'],
            'prices' => ['nullable', 'array'],
            'prices.*.currency_id' => ['required_with:prices', 'integer', 'exists:currencies,id'],
            'prices.*.price' => ['required_with:prices', 'numeric', 'min:0'],
        ];
    }
}
