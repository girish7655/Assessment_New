<?php

namespace App\Repositories\Interfaces;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function getBookReviews(string $bookId): LengthAwarePaginator;
    public function getBookDetails(string $bookId): Book;
    public function findById(string $id): ?Review;
    public function create(array $data): Review;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getUserReviewForBook(string $userId, string $bookId): ?Review;
}