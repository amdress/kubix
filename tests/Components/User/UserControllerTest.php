<?php

namespace Tests\Feature\Components\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_user_via_api()
    {
        $payload = [
            'name'     => 'API User',
            'email'    => 'api@test.com',
            'password' => 'secret_api_123',
            'cpf'      => '11122233344',
            'phone'    => '5541988887777'
        ];

        // Si sacaste la ruta de Sanctum, esto pasará directo
        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonPath('data.email', 'api@test.com');
                 
        $this->assertDatabaseHas('users', ['email' => 'api@test.com']);
    }

    /** @test */
    public function it_can_show_user_details()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('data.id', $user->id);
    }
}