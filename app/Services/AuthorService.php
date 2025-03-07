<?php

namespace App\Services;

use App\Models\Author;
use App\Repositories\AuthorRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AuthorService
{
    public function __construct(
        private readonly AuthorRepository $authorRepository
    ) {}

    public function getPaginatedAuthors(): LengthAwarePaginator
    {
        return $this->authorRepository->getAllPaginated();
    }

    public function createAuthor(array $data): Author
    {
        $data['created_by'] = auth()->id();
        return Author::create($data);
    }

    public function updateAuthor(Author $author, array $data): Author
    {
        if (auth()->user()->role === 'librarian' && $author->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        $author->update($data);
        return $author->fresh();
    }

    public function deleteAuthor(Author $author): bool
    {
        if (auth()->user()->role === 'librarian' && $author->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        return $author->delete();
    }
}
