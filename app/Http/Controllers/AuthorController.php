<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Requests\AuthorRequest;
use App\Services\AuthorService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function __construct(
        private readonly AuthorService $authorService
    ) {
        $this->authorizeResource(Author::class, 'author');
    }

    public function index(): Response
    {
        $authors = $this->authorService->getPaginatedAuthors();

        return Inertia::render('Authors/Index', [
            'authors' => $authors,
            'filters' => request()->only(['search'])
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Authors/Create');
    }

    public function store(AuthorRequest $request): RedirectResponse
    {
        try {
            $this->authorService->createAuthor($request->validated());

            return redirect()
                ->route('authors.index')
                ->with('success', 'Author created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', 'Failed to create author. The author might already exist.');
        }
    }

    public function edit(Author $author): Response
    {
        return Inertia::render('Authors/Edit', [
            'author' => $author
        ]);
    }

    public function update(AuthorRequest $request, Author $author): RedirectResponse
    {
        try {
            $this->authorService->updateAuthor($author, $request->validated());

            return redirect()
                ->route('authors.index')
                ->with('success', 'Author updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update author. The author might already exist.');
        }
    }

    public function destroy(Author $author): RedirectResponse
    {
        try {
            $this->authorService->deleteAuthor($author);

            return redirect()
                ->route('authors.index')
                ->with('success', 'Author deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', 'Failed to delete author. Please try again.');
        }
    }
}
