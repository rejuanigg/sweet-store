<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'=>'string|min:3|max:150',
            'description'=>'string|min:3|max:70000',
            'price'=>'numeric|min:0',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
            'is_featured' => 'sometimes|boolean',
            'featured_order' => 'nullable|integer|min:1|max:4',
        ];
    }
}
