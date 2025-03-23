<?php

namespace App\Repositories;

use App\Models\Response;
use App\Models\Ticket;

class ResponseRepository
{
    protected $responseModel;

    public function __construct(Response $responseModel)
    {
        $this->responseModel = $responseModel;
    }

    public function create(array $data)
    {
        return $this->responseModel->create($data);
    }

    public function findByTicketId(int $ticketId)
    {
        return $this->responseModel
            ->with('agent:id,name')
            ->where('ticket_id', $ticketId)
            ->latest()
            ->get();
    }

    public function getLatestForTicket(Ticket $ticket)
    {
        return $ticket->responses()
            ->with('agent:id,name')
            ->latest()
            ->first();
    }
}