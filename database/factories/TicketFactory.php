<?php

namespace Database\Factories;

use App\Enums\TicketState;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->paragraph(10),
            'description' => fake()->text(20),
            'attachment_path' => fake()->filePath(),
            'state' => TicketState::Submitted,
        ];
    }
}
