<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Enums\TicketState;
use App\Events\TicketStateChanged;
use App\Jobs\SendTicketToExternalServiceJob;
use App\Notifications\TicketApprovedNotification;

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

        //Firing event for logging on ticket creation
        event(new TicketStateChanged(ticket: $ticket, from: null, to: TicketState::Submitted, performedBy: $user->id));

        return $ticket;
    }

    public function approveByAdmin1(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::ApprovedByAdmin1;
        $from = $ticket->state;

        $this->TicketStateChange($ticket, $from, $to, $admin);

        return $ticket;
    }

    public function rejectByAdmin1(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::RejectedByAdmin1;
        $from = $ticket->state;

        $this->TicketStateChange($ticket, $from, $to, $admin);

        return $ticket;
    }

    public function approveByAdmin2(Ticket $ticket, User $admin): Ticket
    {
        $to = TicketState::ExternalProcessing;
        $from = $ticket->state;

        $this->TicketStateChange($ticket, $from, $to, $admin);

        //When approved by saecond admin, sending ticket to external API
        SendTicketToExternalServiceJob::dispatch($ticket->id, $admin->id);

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

        //Firing event for logging on ticket creation
        event(new TicketStateChanged(
            ticket: $ticket,
            from: $from,
            to: $to,
            performedBy: $admin->id
        ));

        return $ticket;
    }

    private function TicketStateChange(
        Ticket $ticket,
        TicketState $from,
        TicketState $to,
        User $admin)
    {
        //Deciding whether the given state can be reached from current state
        $from->assertCanTransition($to);

        $ticket->update(['state' => $to]);

        //Firing event for logging on ticket creation
        event(new TicketStateChanged(
            ticket: $ticket,
            from: $from,
            to: $to,
            performedBy: $admin->id
        ));

        //Sending notification to user
        $ticket->user->notify(new TicketApprovedNotification($ticket));
    }
}
