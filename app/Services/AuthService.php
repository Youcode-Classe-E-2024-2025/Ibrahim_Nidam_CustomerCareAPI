<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $validatedData)
    {
        $role = 'user';
        if (!empty($validatedData['agent_code'])) {
            if ($this->userRepository->validateAgentCode($validatedData['agent_code'])) {
                $role = 'agent';
            }
        }
        
        $userData = $validatedData;
        unset($userData['agent_code']);
        $userData['role'] = $role;
        
        $user = $this->userRepository->createUser($userData);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(array $credentials)
    {
        $user = $this->userRepository->findUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
        
        return true;
    }
}