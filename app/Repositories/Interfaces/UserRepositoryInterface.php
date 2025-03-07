<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getPaginatedWithRoles(int $perPage = 15): LengthAwarePaginator;
    public function findById(string $id): ?User;
    public function create(array $data): User;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function attachRoles(User $user, array $roleIds): void;
}