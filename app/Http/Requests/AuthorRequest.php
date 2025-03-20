<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorRequest extends FormRequest
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
                Rule::unique('authors')->where(function ($query) {
                    return $query->where('name', $this->name)
                                ->where('birth_date', $this->birth_date)
                                ->where('nationality', $this->nationality)
                                ->where('created_by', auth()->id())
                                ->whereNull('deleted_at');
                })->ignore($this->author)
            ],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'You already have an author with these details.',
            'name.required' => 'The author name is required.',
            'name.max' => 'The author name must not exceed 255 characters.',
            'birth_date.before' => 'The birth date must be before today.',
            'nationality.max' => 'The nationality must not exceed 100 characters.',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalize empty strings to null
        if ($this->has('birth_date') && $this->birth_date === '') {
            $this->merge([
                'birth_date' => null,
            ]);
        }

        if ($this->has('nationality') && $this->nationality === '') {
            $this->merge([
                'nationality' => null,
            ]);
        }
    }
}
