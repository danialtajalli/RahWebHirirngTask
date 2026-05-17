<?php

namespace App\Listeners;

use App\Events\TicketStateChanged;
use App\Models\TicketStateLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTicketStateChange implements ShouldQueue
{
    public $queue = 'logs';
    public function handle(TicketStateChanged $event): void
    {
        TicketStateLog::create([
            'ticket_id' => $event->ticket->id,
            'from_state' => $event->from?->value,
            'to_state' => $event->to->value,
            'performed_by' => $event->performedBy,
        ]);
    }
}
