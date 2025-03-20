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
    /**
     * Construct a new AuthorController instance.
     *
     * @param  AuthorService  $authorService
     */
    public function __construct(
        private readonly AuthorService $authorService
    ) {
        $this->authorizeResource(Author::class, 'author');
    }

    /**
     * Show a paginated list of authors with search.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        $authors = $this->authorService->getPaginatedAuthors();

        return Inertia::render('Authors/Index', [
            'authors' => $authors,
            'filters' => request()->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new author.
     *
     * @return \Inertia\Response
     */

    public function create(): Response
    {
        return Inertia::render('Authors/Create');
    }

    /**
     * Store a newly created author in storage.
     *
     * This action is responsible for saving a new author to the database.
     *
     * @param  AuthorRequest  $request
     * @return RedirectResponse
     */
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

    /**
     * Show the form for editing the specified author.
     *
     * Authorizes the edit action for the provided author, then renders the edit form with the author data.
     *
     * @param  \App\Models\Author  $author
     * @return \Inertia\Response
     */
    public function edit(Author $author): Response
    {
        return Inertia::render('Authors/Edit', [
            'author' => $author
        ]);
    }

    /**
     * Update the specified author in storage.
     *
     * Validates and updates the author with the provided data.
     * Redirects back to the authors index on success, or back with error messages on failure.
     *
     * @param  AuthorRequest  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

    /**
     * Remove the specified author from storage.
     *
     * Deletes the author from the database. Redirects back to the authors index
     * on success, or back with error messages on failure.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\RedirectResponse
     */
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
