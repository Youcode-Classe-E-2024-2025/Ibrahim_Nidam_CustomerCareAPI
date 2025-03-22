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
    
        if ($ticket->status !== 'open' || $ticket->assigned_agent_id !== null) {
            abort(409, 'Ticket is no longer available');
        }
    
        // Use the ticket instance directly
        return $this->ticketRepository->assignAgent($ticket->id, $agent->id);
    }

    public function getTicketWithAgent(Ticket $ticket)
    {
        return $this->ticketRepository->getWithAgent($ticket->id);
    }
}