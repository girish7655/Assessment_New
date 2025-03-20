<?php

namespace App\Repositories\Eloquent;

use App\Models\Publisher;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PublisherRepository implements PublisherRepositoryInterface
{
    protected function scopeForCurrentUser($query)
    {
        if (auth()->user()->role === 'librarian') {
            // Librarians can only see their own publishers
            return $query->where('created_by', auth()->id());
        }
        
        // Customers can see all publishers created by librarians
        return $query->whereHas('creator', function($query) {
            $query->where('role', 'librarian');
        });
    }

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->scopeForCurrentUser(Publisher::query())
            ->when(request('search'), function ($query, $search) {
                $query->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->with('creator')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Publisher
    {
        return $this->scopeForCurrentUser(Publisher::query())
            ->findOrFail($id);
    }

    private function validateUniqueName(string $name, ?int $excludeId = null): void
    {
        $query = Publisher::query()
            ->where('name', $name)
            ->where('created_by', auth()->id()) 
            ->whereNull('deleted_at');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => ['You already have a publisher with this name.']
            ]);
        }
    }

    public function create(array $data): Publisher
    {
        $this->validateUniqueName($data['name']);
        
        $data['created_by'] = auth()->id();
        return Publisher::create($data);
    }

    public function update(Publisher $publisher, array $data): Publisher
    {
        if (auth()->user()->role === 'librarian' && $publisher->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        if (isset($data['name']) && $data['name'] !== $publisher->name) {
            $this->validateUniqueName($data['name'], $publisher->id);
        }

        $publisher->update($data);
        return $publisher->fresh();
    }

    public function delete(Publisher $publisher): bool
    {
        if (auth()->user()->role === 'librarian' && $publisher->created_by !== auth()->id()) {
            throw new \Exception('Unauthorized action.');
        }

        if ($publisher->books()->exists()) {
            throw new \Exception('Cannot delete publisher. It has associated books.');
        }

        return $publisher->forceDelete();
    }

    public function getSelectList(): array
    {
        return $this->scopeForCurrentUser(Publisher::query())
            ->select('id', 'name')
            ->get()
            ->toArray();
    }
}
