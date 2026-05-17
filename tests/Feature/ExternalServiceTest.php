<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Jobs\SendTicketToExternalServiceJob;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Queue;
use App\Enums\TicketState;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExternalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_2_dispatches_external_job(): void
    {
        Queue::fake();

        $admin = User::factory()->admin2()->create();

        $ticket = Ticket::factory()->create([
            'state' => TicketState::ApprovedByAdmin1
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/approve-admin-2"
        );

        $response->assertOk();

        Queue::assertPushed(
            SendTicketToExternalServiceJob::class
        );
    }
}
