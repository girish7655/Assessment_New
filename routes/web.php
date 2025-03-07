<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookCheckoutController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Books routes
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create')->middleware('can:create,App\Models\Book');
    Route::post('/books', [BookController::class, 'store'])->name('books.store')->middleware('can:create,App\Models\Book');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit')->middleware('can:update,book');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update')->middleware('can:update,book');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy')->middleware('can:delete,book');
    Route::post('/books/{book}/checkout', [BookCheckoutController::class, 'checkout'])->name('books.checkout')->middleware(['auth', 'role:customer']);
    Route::post('/books/{book}/return', [BookCheckoutController::class, 'return'])->name('books.return')->middleware(['auth', 'role:librarian']);

    // Authors routes with authorization
    Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('/authors/create', [AuthorController::class, 'create'])->middleware('can:create,App\Models\Author')->name('authors.create');
    Route::post('/authors', [AuthorController::class, 'store'])->middleware('can:create,App\Models\Author')->name('authors.store');
    Route::get('/authors/{author}/edit', [AuthorController::class, 'edit'])->middleware('can:update,author')->name('authors.edit');
    Route::put('/authors/{author}', [AuthorController::class, 'update'])->middleware('can:update,author')->name('authors.update');
    Route::delete('/authors/{author}', [AuthorController::class, 'destroy'])->middleware('can:delete,author')->name('authors.destroy');

    // Categories routes with authorization
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->middleware('can:create,App\Models\Category')->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('can:create,App\Models\Category')->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->middleware('can:update,category')->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('can:update,category')->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('can:delete,category')->name('categories.destroy');

    // Publishers routes with authorization
    Route::get('/publishers', [PublisherController::class, 'index'])->name('publishers.index');
    Route::get('/publishers/create', [PublisherController::class, 'create'])->middleware('can:create,App\Models\Publisher')->name('publishers.create');
    Route::post('/publishers', [PublisherController::class, 'store'])->middleware('can:create,App\Models\Publisher')->name('publishers.store');
    Route::get('/publishers/{publisher}/edit', [PublisherController::class, 'edit'])->middleware('can:update,publisher')->name('publishers.edit');
    Route::put('/publishers/{publisher}', [PublisherController::class, 'update'])->middleware('can:update,publisher')->name('publishers.update');
    Route::delete('/publishers/{publisher}', [PublisherController::class, 'destroy'])->middleware('can:delete,publisher')->name('publishers.destroy');
    Route::get('/publishers/{publisher}', [PublisherController::class, 'show'])->name('publishers.show');

    // Book Reviews routes
    Route::get('/books/{bookId}/reviews', [ReviewController::class, 'index'])->name('books.reviews.index');
    Route::post('/books/{bookId}/reviews', [ReviewController::class, 'store'])->name('books.reviews.store');
    Route::put('/books/{bookId}/reviews/{id}', [ReviewController::class, 'update'])->name('books.reviews.update');
    Route::delete('/books/{bookId}/reviews/{id}', [ReviewController::class, 'destroy'])->name('books.reviews.destroy');
});
