<?php

namespace App\Repositories;

use App\Models\Ticket;

class TicketRepository
{
    protected $ticketModel;

    public function __construct(Ticket $ticketModel)
    {
        $this->ticketModel = $ticketModel;
    }

    public function create(array $data)
    {
        return $this->ticketModel->create($data);
    }

    public function findById(int $id)
    {
        return $this->ticketModel->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $ticket = $this->findById($id);
        $ticket->update($data);
        return $ticket;
    }

    public function delete(int $id)
    {
        $ticket = $this->findById($id);
        $ticket->delete();
        return $ticket;
    }

    public function getAllForCustomer(int $customerId)
    {
        return $this->ticketModel->where('customer_id', $customerId)->get();
    }

    public function getAllForAgent(int $agentId)
    {
        return $this->ticketModel->where('assigned_agent_id', $agentId)->get();
    }
}