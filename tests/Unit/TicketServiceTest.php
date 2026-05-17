<?php

namespace Tests\Unit;

use App\Enums\TicketState;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_admin1_can_approve_ticket()
    {
        $service = app(TicketService::class);

        $admin = User::factory()->admin1()->create();

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        $service->approveByAdmin1($ticket, $admin);

        $this->assertEquals(
            TicketState::ApprovedByAdmin1,
            $ticket->fresh()->state
        );
    }
    public function test_admin1_can_reject_ticket()
    {
        $service = app(TicketService::class);

        $admin = User::factory()->admin1()->create();

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        $service->rejectByAdmin1($ticket, $admin);

        $this->assertEquals(
            TicketState::RejectedByAdmin1,
            $ticket->fresh()->state
        );
    }


}
