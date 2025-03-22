<?php

namespace App\Policies;

use App\Models\User;

class TicketPolicy
{
    public function viewAvailableTickets(User $user)
    {
        return $user->isAgent();
    }
}