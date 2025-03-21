<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function createUser(array $userData);
    public function findUserByEmail(string $email);
    public function validateAgentCode(string $code);
}