<?php

namespace App\Jobs;

use App\Contracts\ExternalTicketServiceInterface;
use App\Enums\TicketState;
use App\Events\ExternalApiCalled;
use App\Events\TicketStateChanged;
use App\Models\Ticket;
use App\Notifications\TicketExternalFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendTicketToExternalServiceJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [
            3600,
            3600,
            3600
        ];
    }


    /**
     * Create a new job instance.
     */
    public function __construct(public int $ticket_id, public int $user_id)
    {
        $this->onQueue('api_call');
    }

    /**
     * Execute the job.
     */
    public function handle(ExternalTicketServiceInterface $externalService): void
    {
        $ticket = Ticket::findOrFail($this->ticket_id);
        $from = $ticket->state;

        $response = $externalService->send($ticket);

        event(new ExternalApiCalled(ticket: $ticket, status_code: $response['status_code'], response_body: $response['body'], success: $response['success'], attempted_at: now()));

        $ticket->increment('external_attempts');

        $ticket->update([
            'last_external_attempt_at' => now()
        ]);

        if (!$response['success'])
            throw new \Exception('External service failed');

        $ticket->update([
            'state' => TicketState::Success
        ]);

        event(new TicketStateChanged(ticket: $ticket, from: $from, to: TicketState::Success, performedBy: $this->user_id));
    }

    public function failed(Throwable $exception): void
    {
        $ticket = Ticket::findOrFail($this->ticket_id);
        $from = $ticket->state;

        $ticket->update([
            'state' => TicketState::ExternalFailed
        ]);

        event(new TicketStateChanged(ticket: $ticket, from: $from, to: TicketState::ExternalFailed, performedBy: $this->user_id));

        $ticket->user->notify(
            new TicketExternalFailedNotification($ticket->id)
        );
    }
}
