<?php

namespace App\Repositories\Interfaces;

use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?Book;
    public function create(array $data): Book;
    public function update(Book $book, array $data): Book;
    public function delete(Book $book): bool;
    public function count(): int;
    public function countByUser(int $userId): int;
}
