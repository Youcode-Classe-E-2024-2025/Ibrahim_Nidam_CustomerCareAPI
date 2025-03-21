<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="CustomerCareAPI",
 *     version="1.0.0",
 *     description="Customer Care API for ticket management"
 * )
 */
class AuthController extends Controller
{
    
}