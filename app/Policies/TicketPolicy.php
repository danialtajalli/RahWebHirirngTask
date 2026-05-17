<?php

namespace App\Policies;

use App\Enums\TicketState;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

//Deciding policy for whether and admin can approve or can reject a ticket
class TicketPolicy
{
    public function approveAdmin1(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::ADMIN_1
            && ($ticket->state === TicketState::Submitted ||
                $ticket->state === TicketState::RejectedByAdmin1);
    }

    public function approveAdmin2(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::ADMIN_2
            && ($ticket->state === TicketState::ApprovedByAdmin1 ||
                $ticket->state === TicketState::RejectedByAdmin2);
    }

    public function rejectAdmin1(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::ADMIN_1
            && ($ticket->state === TicketState::ApprovedByAdmin1 ||
                $ticket->state === TicketState::Submitted);
    }

    public function rejectAdmin2(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::ADMIN_2
            && ($ticket->state === TicketState::ExternalProcessing ||
                $ticket->state === TicketState::ApprovedByAdmin1);;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->role === UserRole::USER)
            return $ticket->user_id === $user->id;

        return true;
    }
}
