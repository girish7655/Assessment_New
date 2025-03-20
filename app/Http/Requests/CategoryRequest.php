<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isLibrarian();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')
                    ->where(function ($query) {
                        return $query->where('created_by', auth()->id())
                                   ->whereNull('deleted_at');
                    })
                    ->ignore($this->category)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name must not exceed 255 characters.',
            'name.unique' => 'You already have a category with this name.',
            'description.max' => 'The description must not exceed 1000 characters.',
        ];
    }
}
