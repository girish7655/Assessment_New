<?php

namespace App\Repositories\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Role;
    public function getLibrarianRole(): Role;
    public function getCustomerRole(): Role;
}