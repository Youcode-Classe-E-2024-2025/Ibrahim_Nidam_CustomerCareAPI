<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function createUser(array $userData)
    {
        return User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
        ]);
    }

    public function findUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function validateAgentCode(string $code)
    {
        return $code === 'AGENT0';
    }
}