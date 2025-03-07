<?php

namespace App\Actions\Reviews;

use App\Models\User;
use App\Models\Book;
use App\Exceptions\DuplicateReviewException;
use App\Repositories\Interfaces\ReviewRepositoryInterface;

class CreateReviewAction
{
    public function __construct(
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    public function execute(array $data, Book $book, User $user): void
    {
        $existingReview = $this->reviewRepository->getUserReviewForBook($user->id, $book->id);
        
        if ($existingReview) {
            throw new DuplicateReviewException('You have already reviewed this book.');
        }

        $this->reviewRepository->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);
    }
}