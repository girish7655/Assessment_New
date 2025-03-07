<?php

namespace App\Repositories\Eloquent;

use App\Models\BookCheckout;
use App\Repositories\Interfaces\BookCheckoutRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BookCheckoutRepository implements BookCheckoutRepositoryInterface
{
    public function __construct(private readonly BookCheckout $model) {}

    public function getCurrentUserCheckouts(): LengthAwarePaginator
    {
        return $this->model
            ->with(['book', 'user'])
            ->where('user_id', auth()->id())
            ->where('return_date', null)
            ->latest()
            ->paginate(15);
    }

    public function getOverdueCheckouts(): Collection
    {
        return $this->model
            ->with(['book', 'user'])
            ->where('due_date', '<', now())
            ->where('return_date', null)
            ->latest()
            ->get();
    }

    public function findById(string $id): ?BookCheckout
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): BookCheckout
    {
        return $this->model->create($data);
    }

    public function getCurrentCheckoutsCount(string $userId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('return_date', null)
            ->count();
    }

    public function markAsReturned(BookCheckout $checkout): bool
    {
        return $checkout->update(['return_date' => now()]);
    }
}
