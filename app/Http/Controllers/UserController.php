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
use App\Models\User;

class UserController extends Controller
{
    /**
     * @param  UserRepositoryInterface  $userRepository
     * @param  RoleRepositoryInterface  $roleRepository
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository

    ) {}

    /**
     * Show the list of users.
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Users/Index', [
            'users' => $this->userRepository->getPaginatedWithRoles(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'roles' => $this->roleRepository->getAll(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  StoreUserRequest  $request
     * @param  CreateUserAction  $createUser
     * @return RedirectResponse
     */
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

    /**
     * Show the form for editing the specified user.
     *
     * @param  string  $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $user = $this->userRepository->findById($id);
        $this->authorize('update', $user);

        return Inertia::render('Users/Edit', [
            'user' => $user->toArray(),
            'roles' => $this->roleRepository->getAll(),
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  string  $id
     * @param  UpdateUserRequest  $request
     * @param  UpdateUserAction  $updateUser
     * @return RedirectResponse
     */

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

    /**
     * Remove the specified user from storage.
     *
     * @param  string  $id
     * @return RedirectResponse
     */

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
