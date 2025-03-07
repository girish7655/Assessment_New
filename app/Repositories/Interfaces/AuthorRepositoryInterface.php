<?php

namespace App\Repositories\Interfaces;

use App\Models\Author;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuthorRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?Author;
    public function create(array $data): Author;
    public function update(Author $author, array $data): Author;
    public function delete(Author $author): bool;
}