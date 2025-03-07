<?php

namespace App\Repositories\Interfaces;

use App\Models\Publisher;
use Illuminate\Pagination\LengthAwarePaginator;

interface PublisherRepositoryInterface
{
    public function getAllPaginated(int $perPage): LengthAwarePaginator;
    public function getSelectList(): array;
    public function create(array $data): Publisher;
    public function update(Publisher $publisher, array $data): Publisher;
    public function delete(Publisher $publisher): bool;
    public function findById(int $id): ?Publisher;
}
