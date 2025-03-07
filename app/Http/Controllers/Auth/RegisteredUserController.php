<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Users\StoreUserRequest;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->registrationService->register($request->validated());

        auth()->login($user);

        return redirect(route('verification.notice'));
    }
}
