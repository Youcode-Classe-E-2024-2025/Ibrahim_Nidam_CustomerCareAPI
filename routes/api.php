<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('tickets', TicketController::class)
        ->where(['ticket' => '[0-9]+']);

    Route::get('/tickets/available', [TicketController::class, 'availableTickets']);
    Route::post('/tickets/{ticket}/claim', [TicketController::class, 'claim']);
    Route::patch('/tickets/{ticket}/resolve', [TicketController::class, 'resolve']);

    Route::post('/tickets/{ticket}/responses', [ResponseController::class, 'store']);
    Route::get('/tickets/{ticket}/responses', [ResponseController::class, 'index']);
});