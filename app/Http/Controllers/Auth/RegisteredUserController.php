<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Users\StoreUserRequest;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Construct a new RegisteredUserController instance.
     *
     * @param  RegistrationService  $registrationService  The registration service.
     */
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Show the form for creating a new user.
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \App\Http\Requests\Users\StoreUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->registrationService->register($request->validated());

        auth()->login($user);

        return redirect(route('verification.notice'));
    }
}
