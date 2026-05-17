<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class TicketCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_ticket(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        //Creating a file object to use in test
        $stub = __DIR__.'/a.pdf';
        $name = 'a2.pdf';
        $path = sys_get_temp_dir().'/'.$name;
        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'pdf', null, true);

        $response = $this->postJson('/api/tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'attachment_path' => $file,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tickets', [
        'title' => 'Test Ticket'
        ]);
    }
}
