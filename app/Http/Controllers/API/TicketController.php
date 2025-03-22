<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="subject", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="customer_id", type="integer"),
 *     @OA\Property(property="assigned_agent_id", type="integer", nullable=true),
 *     @OA\Property(property="status", type="string", enum={"open", "in_progress", "resolved"}),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="TicketWithAgent",
 *     allOf={@OA\Schema(ref="#/components/schemas/Ticket")},
 *     @OA\Property(
 *         property="assigned_agent",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="email", type="string")
 *     )
 * )
 */
class TicketController extends Controller
{
    use AuthorizesRequests;
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * @OA\Get(
     *     path="/api/tickets",
     *     summary="Get user's tickets",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index(): JsonResponse
    {
        $tickets = $this->ticketService->getAllTicketsForUser(Auth::user());
        return response()->json($tickets);
    }

    /**
     * @OA\Post(
     *     path="/api/tickets",
     *     summary="Create a ticket",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(response=201, description="Ticket created"),
     * )
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($request->validated(), Auth::user());
        return response()->json($ticket, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/{id}",
     *     summary="Get a ticket",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TicketWithAgent")
     *     ),
     * )
     */
    public function show(Ticket $ticket): JsonResponse 
    {
        $ticketWithAgent = $this->ticketService->getTicketWithAgent($ticket);
        return response()->json($ticketWithAgent);
    }

    /**
     * @OA\Put(
     *     path="/api/tickets/{id}",
     *     summary="Update a ticket",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(response=200, description="Ticket updated"),
     * )
     */
    public function update(UpdateTicketRequest $request, int $id): JsonResponse
    {
        $ticket = $this->ticketService->updateTicket($id, $request->validated(), Auth::user());
        return response()->json($ticket);
    }

    /**
     * @OA\Delete(
     *     path="/api/tickets/{id}",
     *     summary="Delete a ticket",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Ticket deleted"),
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->ticketService->deleteTicket($id, Auth::user());
        return response()->json(['message' => 'Ticket deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/available",
     *     summary="Get available open tickets",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success"),
     * )
     */
    public function availableTickets(): JsonResponse
    {
        try {
            $this->authorize('viewAvailableTickets', Ticket::class);
            
            $tickets = $this->ticketService->getAvailableTickets();
            return response()->json($tickets);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tickets/{id}/claim",
     *     summary="Claim a ticket",
     *     tags={"Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Ticket claimed"),
     * )
     */
    public function claim(Ticket $ticket): JsonResponse
    {
        // Pass the Ticket model directly
        $updatedTicket = $this->ticketService->claimTicket($ticket, Auth::user());
        return response()->json([
            'message' => 'Ticket claimed successfully',
            'ticket' => $updatedTicket
        ]);
    }

}