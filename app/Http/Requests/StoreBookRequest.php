<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isLibrarian();
    }

    public function rules(): array
    {
        $rules = [
            'title' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('books')
                    ->where(function ($query) {
                        return $query->where('author_id', $this->author_id)
                                   ->whereNull('deleted_at');
                    })
            ],
            'author_id' => ['required', 'exists:authors,id'],
            'publisher_id' => ['required', 'exists:publishers,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'isbn' => [
                'required', 
                'string', 
                Rule::unique('books')
                    ->whereNull('deleted_at')
            ],
            'published_year' => ['required', 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'description' => ['required', 'string'],
        ];

        if (auth()->user()->role === 'librarian') {
            $rules['author_id'][] = Rule::exists('authors', 'id')
                ->where('created_by', auth()->id());
            $rules['publisher_id'][] = Rule::exists('publishers', 'id')
                ->where('created_by', auth()->id());
            $rules['category_id'][] = Rule::exists('categories', 'id')
                ->where('created_by', auth()->id());
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'A book with this title and author already exists.',
            'isbn.unique' => 'A book with this ISBN already exists.',
        ];
    }
}
