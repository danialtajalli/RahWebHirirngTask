<?php

namespace Tests\Unit;

use App\Enums\TicketState;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    //Creating a new ticket and approving by admin 1.
    public function test_admin1_can_approve_ticket()
    {
        $service = app(TicketService::class);

        $admin = User::factory()->admin1()->create();

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        //Creating a random comment for approval description
        $description = fake()->sentence();
        //Using service to approve ticket by admin 1
        $service->approveByAdmin1($ticket, $admin, $description);

        //making sure ticket state is changed.
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
        $description = fake()->sentence();

        $service->rejectByAdmin1($ticket, $admin, $description);

        $this->assertEquals(
            TicketState::RejectedByAdmin1,
            $ticket->fresh()->state
        );
    }

    public function test_ticket_approval_flow()
    {
        $service = app(TicketService::class);

        $admin = User::factory()->admin1()->create();
        $admin2 = User::factory()->admin2()->create();

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        $description = fake()->sentence();

        $service->approveByAdmin1($ticket, $admin, $description);

        $this->assertEquals(
            TicketState::ApprovedByAdmin1,
            $ticket->fresh()->state
        );

        //Faking the connection to external API route. It is protected by sanctum; or maybe changed later.
        //Either way, it is not subject of this test.
        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);
        $service->approveByAdmin2($ticket, $admin2, $description);

        $this->assertEquals(
            TicketState::Success,
            $ticket->fresh()->state
        );
    }

}
