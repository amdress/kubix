<?php

namespace Tests\Feature\Components\Branch;

use Tests\TestCase;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class BranchControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_branches()
    {
        Branch::factory()->count(3)->create();

        $response = $this->getJson('/api/branches');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_create_a_branch_via_api()
    {
        $payload = [
            'name' => 'Nueva Sucursal API',
            'code' => 'API-01',
            'is_active' => true
        ];

        $response = $this->postJson('/api/branches', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('branches', ['code' => 'API-01']);
    }
}