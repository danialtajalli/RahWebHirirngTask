<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Enums\TicketState;
use App\Notifications\TicketApprovedNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TicketRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'state' => TicketState::Submitted
        ]);

        $user->notify(
            new TicketRejectedNotification($ticket)
        );

        Notification::assertSentTo(
            $user,
            TicketRejectedNotification::class
        );

        $user->notify(
            new TicketApprovedNotification($ticket)
        );
        Notification::assertSentTo(
            $user,
            TicketApprovedNotification::class
        );
    }
}
