<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Response;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class ResponseService
{

    public function createResponse(Ticket $ticket, User $agent, array $data): Response
    {
        if ($ticket->assigned_agent_id !== $agent->id) {
            throw new AuthorizationException('Only the assigned agent can respond to this ticket');
        }

        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
            $ticket->save();
        }

        $response = new Response();
        $response->ticket_id = $ticket->id;
        $response->agent_id = $agent->id;
        $response->content = $data['content'];
        $response->save();

        $response->load(['agent:id,name,email']);

        return $response;
    }

    public function getTicketResponses(Ticket $ticket): Collection
    {
        return $ticket->responses()
            ->with(['agent:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}