<?php

namespace App\Services;

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
}