<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublisherRequest extends FormRequest
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
                'min:2',
                'max:255',
                Rule::unique('publishers')
                    ->where(function ($query) {
                        return $query->where('created_by', auth()->id())
                                   ->whereNull('deleted_at');
                    })
                    ->ignore($this->publisher)
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^([0-9\s\-\+\(\)]*)$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The publisher name is required.',
            'name.min' => 'The publisher name must be at least 2 characters.',
            'name.max' => 'The publisher name must not exceed 255 characters.',
            'name.unique' => 'You already have a publisher with this name.',
            'address.max' => 'The address must not exceed 255 characters.',
            'phone.max' => 'The phone number must not exceed 20 characters.',
            'phone.regex' => 'The phone number format is invalid.',
        ];
    }

    protected function prepareForValidation()
    {
        // Trim whitespace from inputs
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->name)]);
        }
        
        if ($this->has('phone')) {
            $this->merge([
                'phone' => $this->phone ? 
                    preg_replace('/[^0-9\+\-\(\)\s]/', '', trim($this->phone)) : 
                    null
            ]);
        }

        // Convert empty strings to null
        if ($this->has('address') && $this->address === '') {
            $this->merge(['address' => null]);
        }
    }
}
