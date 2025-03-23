<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $ticket->customer_id === $user->id || $ticket->assigned_agent_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $ticket->customer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $ticket->customer_id === $user->id && $ticket->status === 'open';
    }

    /**
     * Determine whether the user can claim the ticket.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function claim(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('agent') && 
               $ticket->status === 'open' && 
               is_null($ticket->assigned_agent_id);
    }

    /**
     * Determine whether the user can resolve the ticket.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function resolve(User $user, Ticket $ticket): bool
    {
        return $ticket->assigned_agent_id === $user->id && 
               $ticket->status !== 'resolved';
    }

    /**
     * Determine whether the user can view available tickets.
     *
     * @param User $user
     * @return bool
     */
    public function viewAvailableTickets(User $user): bool
    {
        return $user->hasRole('agent');
    }

    /**
     * Determine whether the user can respond to the ticket.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function respond(User $user, Ticket $ticket): bool
    {
        return $ticket->assigned_agent_id === $user->id && 
               in_array($ticket->status, ['open', 'in_progress']);
    }

    /**
     * Determine whether the user can view responses for the ticket.
     *
     * @param User $user
     * @param Ticket $ticket
     * @return bool
     */
    public function viewResponses(User $user, Ticket $ticket): bool
    {
        return $ticket->customer_id === $user->id || $ticket->assigned_agent_id === $user->id;
    }
}