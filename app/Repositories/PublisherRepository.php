<?php

namespace App\Repositories;

use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;

class PublisherRepository
{

    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Publisher::query()
            ->when(auth()->user()->role === 'librarian', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
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
        $query = Publisher::query();
        
        if (auth()->user()->role === 'librarian') {
            $query->where('created_by', auth()->id());
        }
        
        return $query->findOrFail($id);
    }
}