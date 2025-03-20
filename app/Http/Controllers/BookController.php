<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Author;

class BookController extends Controller
{
    /**
     * Constructs a new BookController instance.
     *
     * @param  BookService  $bookService  The book service.
     */
    public function __construct(
        private readonly BookService $bookService
    ) {
        $this->authorizeResource(Book::class, 'book');
    }

    /**
     * Renders the book index page.
     *
     * This action is responsible for rendering the page that displays all books.
     *
     * @return Response
     */
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

    /**
     * Renders the book creation page.
     *
     * This action is responsible for rendering the page where a librarian can create a new book.
     *
     * @return Response
     */
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

    /**
     * Store a newly created book in storage.
     *
     * This action is responsible for saving a new book to the database.
     *
     * @param  \App\Http\Requests\StoreBookRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $this->bookService->createBook($request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    /**
     * Displays the details of a specific book.
     *
     * Loads the book's related data including creator, category, publisher, 
     * author, and reviews. Checks if the current user has ever checked out 
     * and returned the book. Determines the user's permissions for updating 
     * and deleting the book based on their role and ownership. Also retrieves 
     * the user's review for the book if it exists.
     *
     * @param  \App\Models\Book  $book
     * @return \Inertia\Response
     */

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

    /**
     * Displays the book edit page.
     *
     * This action is responsible for rendering the page where a librarian can edit a book's details.
     * It checks if the current user is the creator of the book and has the permission to edit it.
     * It also loads the related data for the book including its category, publisher, and author.
     * The categories, publishers, and authors that the librarian has created are also retrieved.
     *
     * @param  \App\Models\Book  $book
     * @return \Inertia\Response
     */
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

    /**
     * Update the specified book in storage.
     *
     * This action is responsible for updating an existing book in the database.
     * It checks if the current user has the permission to edit the book by
     * verifying if the user is a librarian and the creator of the book.
     * If the user has the permission, the action will update the book in the
     * database and redirect the user to the book list page.
     *
     * @param  \App\Http\Requests\UpdateBookRequest  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        if (auth()->user()->role === 'librarian' && auth()->id() !== $book->created_by) {
            abort(403, 'Unauthorized action.');
        }

        $this->bookService->updateBook($book, $request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified book from storage.
     *
     * This action is responsible for deleting an existing book in the database.
     * It checks if the current user has the permission to delete the book by
     * verifying if the user is a librarian and the creator of the book.
     * If the user has the permission, the action will delete the book in the
     * database and redirect the user to the book list page.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
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
