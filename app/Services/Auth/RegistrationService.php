<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Role;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegistrationService
{
    public function register(array $data): User
    {
        $role_name = Role::find($data['role_id'])->name;
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'role' => $role_name,
        ]);

        event(new Registered($user));
        event(new UserRegistered($user));

        return $user;
    }
}
