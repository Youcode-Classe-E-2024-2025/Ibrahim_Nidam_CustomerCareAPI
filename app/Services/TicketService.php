<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Models\User;

class TicketService
{
    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function createTicket(array $data, User $user)
    {
        $data['customer_id'] = $user->id;
        $data['status'] = 'open';
        return $this->ticketRepository->create($data);
    }

    public function getTicket(int $id, User $user)
    {
        $ticket = $this->ticketRepository->findById($id);

        if ($ticket->customer_id !== $user->id && $ticket->assigned_agent_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return $ticket;
    }

    public function updateTicket(int $id, array $data, User $user)
    {
        $ticket = $this->ticketRepository->findById($id);

        // Authorization Check
        if ($ticket->customer_id !== $user->id && $ticket->assigned_agent_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Restrict fields based on user role
        if ($user->isAgent()) {
            $allowedFields = ['status', 'description'];
        } else {
            $allowedFields = ['subject', 'description'];
        }

        $filteredData = array_intersect_key($data, array_flip($allowedFields));
        return $this->ticketRepository->update($id, $filteredData);
    }

    public function deleteTicket(int $id, User $user)
    {
        $ticket = $this->ticketRepository->findById($id);

        if ($ticket->customer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return $this->ticketRepository->delete($id);
    }

    public function getAllTicketsForUser(User $user)
    {
        if ($user->isAgent()) {
            return $this->ticketRepository->getAllForAgent($user->id);
        }
        return $this->ticketRepository->getAllForCustomer($user->id);
    }

    public function getAvailableTickets()
    {
        return $this->ticketRepository->getOpenTickets();
    }

    public function claimTicket(Ticket $ticket, User $agent)
    {
        if (!$agent->isAgent()) {
            abort(403, 'Only agents can claim tickets');
        }

        return $this->ticketRepository->update($ticket->id, [
            'assigned_agent_id' => $agent->id,
            'status' => 'in_progress',
            'claimed_at' => now()
        ]);
    }

    public function getTicketWithAgent(Ticket $ticket)
    {
        return $this->ticketRepository->getWithAgent($ticket->id);
    }

    public function getTicketWithResponses(int $id)
    {
        return $this->ticketRepository->getWithResponses($id);
    }

    public function resolveTicket(Ticket $ticket)
    {
        return $this->ticketRepository->markAsResolved($ticket->id);
    }

    public function addResponse(Ticket $ticket, array $data, User $agent)
    {
        if ($ticket->assigned_agent_id !== $agent->id) {
            abort(403, 'You are not assigned to this ticket');
        }

        return $this->ticketRepository->addResponse($ticket->id, [
            'content' => $data['content'],
            'agent_id' => $agent->id
        ]);
    }

    public function getTicketWithDetails(Ticket $ticket)
    {
        return $this->ticketRepository->getWithDetails($ticket->id);
    }
}