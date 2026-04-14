<?php

namespace Tests\Feature\Components\Address;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Prueba la creación directa (si el controlador tiene el método store)
     */
    public function it_creates_an_address()
    {
        $user = User::factory()->create();
        
        $payload = [
            'street'       => 'Rua das Flores',
            'number'       => '123',
            'neighborhood' => 'Centro',
            'complement'   => 'Apto 1',
            'city'         => 'Curitiba',
            'state'        => 'PR',
            'zip_code'     => '80000-000',
            'country'      => 'Brazil',
            'latitude'     => -25.4296,
            'longitude'    => -49.2719,
            'is_primary'   => true,
            'addressable_type' => get_class($user), // Campos para el store directo
            'addressable_id'   => $user->id
        ];

        $response = $this->postJson('/api/addresses', $payload);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonPath('data.street', 'Rua das Flores');
                 
        $this->assertDatabaseHas('addresses', ['street' => 'Rua das Flores']);
    }

    /**
     * @test
     */
    public function it_shows_an_address()
    {
        $address = Address::factory()->create();

        $response = $this->getJson("/api/addresses/{$address->id}");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('data.id', $address->id);
    }

    /**
     * @test
     */
    public function it_updates_an_address()
    {
        $address = Address::factory()->create();
        
        $payload = [
            'street'       => 'Nueva Avenida',
            'number'       => '500',
            'neighborhood' => 'Bairro Novo',
            'complement'   => null,
            'city'         => 'Curitiba',
            'state'        => 'PR',
            'zip_code'     => '80000-000',
            'country'      => 'Brazil',
            'latitude'     => null,
            'longitude'    => null,
            'is_primary'   => false
        ];

        $response = $this->putJson("/api/addresses/{$address->id}", $payload);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('data.street', 'Nueva Avenida');

        $this->assertDatabaseHas('addresses', ['street' => 'Nueva Avenida']);
    }

    /**
     * @test
     */
    public function it_deletes_an_address()
    {
        $address = Address::factory()->create();

        $response = $this->deleteJson("/api/addresses/{$address->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
    }

    /**
     * @test
     * Prueba el listado filtrado por entidad (byAddressable)
     */
    public function it_lists_addresses_by_addressable()
    {
        $branch = Branch::factory()->create();
        Address::factory()->count(2)->create([
            'addressable_id' => $branch->id,
            'addressable_type' => get_class($branch)
        ]);

        // Simulamos la query string que espera tu controlador
        $url = "/api/addresses?addressable_type=" . urlencode(get_class($branch)) . "&addressable_id=" . $branch->id;
        
        $response = $this->getJson($url);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonCount(2, 'data');
    }
}