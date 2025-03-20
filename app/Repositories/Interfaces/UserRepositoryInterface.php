<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getPaginatedWithRoles();
    public function findById(string $id);
    public function delete(string $id);
}
