<?php

namespace App\Listeners;

use App\Events\ExternalApiCalled;
use App\Models\ExternalApiLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogExternalApiCalled implements ShouldQueue
{
    public $queue = 'logs';

    /**
     * Handle the event.
    */
    public function handle(ExternalApiCalled $event): void
    {
        ExternalApiLog::create([
            'ticket_id' => $event->ticket->id,
            'status_code' => $event->status_code,
            'response_body' => $event->response_body,
            'success' => $event->success,
            'attempted_at' => $event->attempted_at,
        ]);
    }
}
