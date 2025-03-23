<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Response\StoreResponseRequest;
use App\Models\Ticket;
use App\Services\ResponseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    use AuthorizesRequests;
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    /**
     * @OA\Post(
     *     path="/api/tickets/{id}/responses",
     *     summary="Add a response to a ticket",
     *     tags={"Responses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Response")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Response added",
     *         @OA\JsonContent(ref="#/components/schemas/ResponseWithDetails")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Only assigned agents can respond"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found"
     *     ),
     * )
     */
    public function store(StoreResponseRequest $request, Ticket $ticket): JsonResponse
    {
        $this->authorize('respond', $ticket);
        
        $response = $this->responseService->createResponse(
            $ticket,
            Auth::user(),
            $request->validated()
        );
        
        return response()->json($response, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/{id}/responses",
     *     summary="Get all responses for a ticket",
     *     tags={"Responses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of responses",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ResponseWithDetails")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Only ticket owner or assigned agent can view responses"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found"
     *     ),
     * )
     */
    public function index(Ticket $ticket): JsonResponse
    {
        $this->authorize('viewResponses', $ticket);
        
        $responses = $this->responseService->getTicketResponses($ticket);
        
        return response()->json($responses);
    }
}