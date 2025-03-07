<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $model) {}

    public function getPaginatedWithRoles(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('roles')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(string $id): ?User
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->findById($id)->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function attachRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }
}