<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketState;
use App\Events\TicketStateChanged;
use App\Jobs\SendTicketToExternalServiceJob;
use App\Notifications\TicketApprovedNotification;
use App\Notifications\TicketRejectedNotification;

class TicketService
{
    public function createTicket(User $user, array $data): Ticket
    {
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'attachment_path' => $data['attachment_path'] ?? null,
            'state' => TicketState::Submitted,
        ]);

        event(new TicketStateChanged(ticket: $ticket, from: null, to: TicketState::Submitted, performedBy: $user->id));

        return $ticket;
    }

    public function approveByAdmin1(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::ApprovedByAdmin1;
        $from = $ticket->state;
        $from->assertCanTransition($to);

        $ticket->update(['state' => $to]);

        event(new TicketStateChanged(
            ticket: $ticket,
            from: $from,
            to: $to,
            performedBy: $admin->id
        ));

        $ticket->user->notify(new TicketApprovedNotification($ticket));

        return $ticket;
    }

    public function rejectByAdmin1(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::RejectedByAdmin1;
        $from = $ticket->state;
        $from->assertCanTransition($to);

        $ticket->update([
            'state' => $to
        ]);

        event(new TicketStateChanged(
            ticket: $ticket,
            from: $from,
            to: $to,
            performedBy: $admin->id
        ));

        $ticket->user->notify(new TicketRejectedNotification($ticket));

        return $ticket;
    }

    public function approveByAdmin2(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::ExternalProcessing;
        $from = $ticket->state;
        $from->assertCanTransition($to);
        $ticket->update(['state' => $to]);

        event(new TicketStateChanged(
            ticket: $ticket,
            from: $from,
            to: $to,
            performedBy: $admin->id
        ));

        SendTicketToExternalServiceJob::dispatch($ticket->id, $admin->id);

        $ticket->user->notify(new TicketApprovedNotification($ticket));

        return $ticket;
    }

    public function rejectByAdmin2(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::RejectedByAdmin2;
        $from = $ticket->state;
        $from->assertCanTransition($to);

        $ticket->update([
            'state' => $to
        ]);

        event(new TicketStateChanged(
            ticket: $ticket,
            from: $from,
            to: $to,
            performedBy: $admin->id
        ));

        return $ticket;
    }
}
