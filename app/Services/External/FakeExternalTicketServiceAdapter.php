<?php

namespace App\Services\External;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use App\Contracts\ExternalTicketServiceInterface;

class FakeExternalTicketServiceAdapter implements ExternalTicketServiceInterface
{
    //Sending API to current server
    public function send(Ticket $ticket): array
    {
        $response = Http::post(
            config('services.external_ticket.url'),
            [
                'ticket_id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
            ]
        );

        return [
            'success' => $response->successful(),
            'status_code' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
