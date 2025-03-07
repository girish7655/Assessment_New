<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublisherRequest;
use App\Models\Publisher;
use App\Services\PublisherService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublisherController extends Controller
{
    public function __construct(
        private readonly PublisherService $publisherService
    ) {
        $this->authorizeResource(Publisher::class, 'publisher');
    }

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

    public function create(): Response
    {
        $this->authorize('create', Publisher::class);
        
        return Inertia::render('Publishers/Create');
    }

    public function store(PublisherRequest $request): RedirectResponse
    {
        $this->publisherService->create($request->validated());

        return redirect()->route('publishers.index')
            ->with('success', 'Publisher created successfully.');
    }

    public function edit(Publisher $publisher): Response
    {
        $this->authorize('update', $publisher);
        
        return Inertia::render('Publishers/Edit', [
            'publisher' => $publisher
        ]);
    }

    public function update(PublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        $this->publisherService->update($publisher, $request->validated());

        return redirect()->route('publishers.index')
            ->with('success', 'Publisher updated successfully.');
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        $this->authorize('delete', $publisher);
        
        $this->publisherService->delete($publisher);

        return redirect()->route('publishers.index')
            ->with('success', 'Publisher deleted successfully.');
    }
}
