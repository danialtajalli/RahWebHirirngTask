<?php

namespace App\Events;

use App\Enums\TicketState;
use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStateChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public ?TicketState $from,
        public TicketState $to,
        public ?int $performedBy
    ) {
    }
}
