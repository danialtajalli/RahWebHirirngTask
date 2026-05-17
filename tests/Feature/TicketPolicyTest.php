<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Enums\UserRole;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_1_can_approve_first_level(): void
    {
        $policy = new TicketPolicy();

        $admin = User::factory()->admin1()->create();

        $ticket = Ticket::factory()->create();

        $this->assertTrue(
            $policy->approveAdmin1($admin, $ticket)
        );
    }

    public function test_normal_user_cannot_approve(): void
    {
        $policy = new TicketPolicy();

        $user = User::factory()->create();

        $ticket = Ticket::factory()->create();

        $this->assertFalse(
            $policy->approveAdmin1($user, $ticket)
        );
    }
}
