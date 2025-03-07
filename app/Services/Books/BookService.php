<?php

namespace App\Services\Books;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Support\Facades\Log;

class BookService
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function getAllBooks()
    {
        return $this->bookRepository->getAllPaginated();
    }

    public function createBook(array $data): Book
    {
        try {
            Log::info('Creating new book', ['data' => $data]);
            $book = $this->bookRepository->create($data);
            Log::info('Book created successfully', ['book_id' => $book->id]);
            return $book;
        } catch (\Exception $e) {
            Log::error('Failed to create book', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateBook(Book $book, array $data): Book
    {
        try {
            Log::info('Updating book', ['book_id' => $book->id, 'data' => $data]);
            $updatedBook = $this->bookRepository->update($book, $data);
            Log::info('Book updated successfully', ['book_id' => $book->id]);
            return $updatedBook;
        } catch (\Exception $e) {
            Log::error('Failed to update book', [
                'book_id' => $book->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteBook(Book $book): void
    {
        $this->bookRepository->delete($book);
    }
}
