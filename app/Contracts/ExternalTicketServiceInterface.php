<?php

namespace App\Contracts;

use App\Models\Ticket;

//Interface for adapter pattern, used when connecting to the external API
interface ExternalTicketServiceInterface
{
    public function send(Ticket $ticket): array;
}
