<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\Books\BookService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Author;

class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService
    ) {
        $this->authorizeResource(Book::class, 'book');
    }

    public function index(): Response
    {
        $isLibrarian = auth()->user()->role === 'librarian';
        $books = $this->bookService->getAllBooks();

        $query = fn($model) => $model::query()
            ->when($isLibrarian, function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => request()->only(['search', 'rating', 'status', 'sortBy']),
            'categories' => $query(Category::class),
            'authors' => $query(Author::class),
            'publishers' => $query(Publisher::class),
            'can' => [
                'create' => $isLibrarian
            ]
        ]);
    }

    public function create(): Response
    {
        $query = fn($model) => $model::query()
            ->when(auth()->user()->role === 'librarian', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();

        return Inertia::render('Books/Create', [
            'categories' => $query(Category::class),
            'publishers' => $query(Publisher::class),
            'authors' => $query(Author::class),
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $this->bookService->createBook($request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    public function show(Book $book): Response
    {
        $user = auth()->user();
        $isLibrarian = $user->role === 'librarian';

        $book->load([
            'creator',
            'category',
            'publisher',
            'author',
            'reviews' => function ($query) use ($user) {
                $query->with('user');
            }
        ]);

        $book->loadCount('reviews');
        $book->loadAvg('reviews', 'rating');

        // Get user's review if exists
        $userReview = $book->reviews->where('user_id', $user->id)->first();

        // Check if user has ever checked out and returned this book
        $hasCheckedOutBook = $book->hasBeenCheckedOutByUser($user);

        return Inertia::render('Books/Show', [
            'book' => $book,
            'can' => [
                'update' => $isLibrarian && auth()->id() === $book->created_by,
                'delete' => $isLibrarian && auth()->id() === $book->created_by
            ],
            'hasCheckedOutBook' => $hasCheckedOutBook,
            'userReview' => $userReview,
            'auth' => [
                'user' => $user
            ]
        ]);
    }

    public function edit(Book $book): Response
    {
        if (auth()->user()->role === 'librarian' && $book->created_by !== auth()->id()) {
            abort(403);
        }

        $query = fn($model) => $model::query()
            ->when(auth()->user()->role === 'librarian', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();

        return Inertia::render('Books/Edit', [
            'book' => $book->load(['category', 'publisher', 'author']),
            'categories' => $query(Category::class),
            'publishers' => $query(Publisher::class),
            'authors' => $query(Author::class),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        if (auth()->user()->role === 'librarian' && auth()->id() !== $book->created_by) {
            abort(403, 'Unauthorized action.');
        }

        $this->bookService->updateBook($book, $request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if (auth()->user()->role === 'librarian' && auth()->id() !== $book->created_by) {
            abort(403, 'Unauthorized action.');
        }

        $this->bookService->deleteBook($book);

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}
