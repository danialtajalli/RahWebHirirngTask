<?php

namespace App\Contracts;

use App\Models\Ticket;

interface ExternalTicketServiceInterface
{
    public function send(Ticket $ticket): array;
}
