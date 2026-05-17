<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Enums\UserRole;
use Laravel\Sanctum\Sanctum;
use App\Enums\TicketState;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_1_can_approve_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN_1
        ]);

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/approve-admin-1",
            ['comment' => fake()->sentence()]
        );

        $response->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => TicketState::ApprovedByAdmin1->value
        ]);

        $ticket = Ticket::factory()->create([
            'state' => TicketState::RejectedByAdmin1
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/approve-admin-1",
            ['comment' => fake()->sentence()]
        );

        $response->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => TicketState::ApprovedByAdmin1->value
        ]);
    }

    public function test_admin_1_can_reject_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN_1
        ]);

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/reject-admin-1",
            ['comment' => fake()->sentence()]
        );

        $response->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => TicketState::RejectedByAdmin1->value
        ]);

        $ticket = Ticket::factory()->create([
            'state' => TicketState::ApprovedByAdmin1
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/reject-admin-1",
            ['comment' => fake()->sentence()]
        );

        $response->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => TicketState::RejectedByAdmin1->value
        ]);
    }

    public function test_admin_1_cant_approve_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN_1
        ]);

        $ticket = Ticket::factory()->create([
            'state' => TicketState::RejectedByAdmin2
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/approve-admin-1",
            ['comment' => fake()->sentence()]
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'state' => TicketState::ApprovedByAdmin1->value
        ]);
    }

    public function test_admin_2_cant_approve_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN_2
        ]);

        $ticket = Ticket::factory()->create([
            'state' => TicketState::Submitted
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            "/api/admin/tickets/{$ticket->id}/approve-admin-2",
            ['comment' => fake()->sentence()]
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'state' => TicketState::ExternalProcessing->value
        ]);
    }
}
