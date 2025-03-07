<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Users/Index', [
            'users' => $this->userRepository->getPaginatedWithRoles(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'roles' => $this->roleRepository->getAll(),
        ]);
    }

    public function store(
        StoreUserRequest $request, 
        CreateUserAction $createUser
    ): RedirectResponse {
        $this->authorize('create', User::class);

        $createUser->execute($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(string $id): Response
    {
        $user = $this->userRepository->findById($id);
        $this->authorize('update', $user);

        return Inertia::render('Users/Edit', [
            'user' => $user->toArray(),
            'roles' => $this->roleRepository->getAll(),
        ]);
    }

    public function update(
        string $id,
        UpdateUserRequest $request, 
        UpdateUserAction $updateUser
    ): RedirectResponse {
        $user = $this->userRepository->findById($id);
        $this->authorize('update', $user);

        $updateUser->execute($user, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $user = $this->userRepository->findById($id);
        $this->authorize('delete', $user);

        $this->userRepository->delete($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
