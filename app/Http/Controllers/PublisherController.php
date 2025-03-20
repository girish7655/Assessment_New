<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Models\Publisher;
use App\Services\PublisherService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class PublisherController extends Controller
{
    public function __construct(
        private readonly PublisherService $publisherService
    ) {
        $this->authorizeResource(Publisher::class, 'publisher');
    }

    /**
     * Returns a paginated list of publishers.
     * 
     * Checks if the authenticated user is a librarian and 
     * includes permissions for each publisher in the response.
     * 
     * @return Response
     */
    public function index(): Response
    {
        $publishers = $this->publisherService->getAllPaginated();
        $isLibrarian = auth()->user()->role === 'librarian';

        // Add permissions for each publisher
        $publishers->through(function ($publisher) use ($isLibrarian) {
            $publisher->can = [
                'update' => $isLibrarian && auth()->id() === $publisher->created_by,
                'delete' => $isLibrarian && auth()->id() === $publisher->created_by
            ];
            return $publisher;
        });

        return Inertia::render('Publishers/Index', [
            'publishers' => $publishers,
            'filters' => request()->only(['search']),
            'can' => [
                'create' => $isLibrarian
            ],
            'isLibrarian' => $isLibrarian
        ]);
    }

    /**
     * Display the specified publisher.
     *
     * Authorizes the user to view the publisher. 
     * Determines if the authenticated user is a librarian and checks 
     * if they have permissions to update or delete the publisher.
     *
     * @param Publisher $publisher
     * @return Response
     */

    public function show(Publisher $publisher): Response
    {
        $this->authorize('view', $publisher);
        $isLibrarian = auth()->user()->role === 'librarian';

        return Inertia::render('Publishers/Show', [
            'publisher' => $publisher->load('creator'),
            'can' => [
                'update' => $isLibrarian && auth()->id() === $publisher->created_by,
                'delete' => $isLibrarian && auth()->id() === $publisher->created_by
            ],
            'isLibrarian' => $isLibrarian
        ]);
    }

    /**
     * Display the form to create a new publisher.
     *
     * @return Response
     */
    public function create(): Response
    {
        $this->authorize('create', Publisher::class);
        
        return Inertia::render('Publishers/Create');
    }

    /**
     * Store a newly created publisher in storage.
     *
     * @param PublisherRequest $request
     * @return RedirectResponse
     *
     * Validates the incoming request data and attempts to create a new publisher.
     * Redirects to the publishers index page with a success message on success.
     * If validation fails, redirects back with validation errors.
     * If any other exception occurs, redirects back with an error message.
     */

    /**
     * Store a newly created publisher in storage.
     *
     * Validates the request data and creates a new publisher using the PublisherService.
     * On success, redirects to the publishers index with a success message.
     * If validation fails, redirects back with validation errors.
     * If an exception occurs, redirects back with an error message.
     *
     * @param PublisherRequest $request
     * @return RedirectResponse
     */

    public function store(PublisherRequest $request): RedirectResponse
    {
        try {
            $this->publisherService->create($request->validated());

            return redirect()
                ->route('publishers.index')
                ->with('success', 'Publisher created successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified publisher.
     *
     * Authorizes the edit action for the provided publisher, then renders the edit form with the publisher data.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Inertia\Response
     */
    public function edit(Publisher $publisher): Response
    {
        $this->authorize('update', $publisher);
        
        return Inertia::render('Publishers/Edit', [
            'publisher' => $publisher
        ]);
    }

    /**
     * Update the specified publisher in storage.
     *
     * Validates and updates the publisher with the provided data.
     * Redirects back to the publishers index on success, or back with error messages on failure.
     *
     * @param  \App\Http\Requests\PublisherRequest  $request
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */

    /**
     * Updates the specified publisher in storage.
     *
     * Validates and updates the publisher with the provided data.
     * Redirects back to the publishers index on success, or back with error messages on failure.
     *
     * @param  \App\Http\Requests\PublisherRequest  $request
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(PublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        try {
            $this->publisherService->update($publisher, $request->validated());

            return redirect()
                ->route('publishers.index')
                ->with('success', 'Publisher updated successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publisher $publisher): RedirectResponse
    {
        $this->authorize('delete', $publisher);
        
        $this->publisherService->delete($publisher);

        return redirect()->route('publishers.index')
            ->with('success', 'Publisher deleted successfully.');
    }
}
