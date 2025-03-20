<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function getAll(): Collection
    {
        return Role::all();
    }

    public function findById(int $id): ?Role
    {
        return Role::find($id);
    }

    public function getLibrarianRole(): Role
    {
        return Role::getLibrarianRole();
    }

    public function getCustomerRole(): Role
    {
        return Role::getCustomerRole();
    }
}