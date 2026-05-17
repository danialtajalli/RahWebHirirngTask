<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_ticket(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tickets', [
        'title' => 'Test Ticket'
        ]);
    }
}
