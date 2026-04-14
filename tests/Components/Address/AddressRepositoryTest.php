<?php

namespace Tests\Unit\Components\Address;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Components\Address\AddressRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AddressRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AddressRepository();
    }

    /** @test */
    public function it_can_create_an_address_for_a_model()
    {
        $user = User::factory()->create();
        $data = [
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
            'is_primary'   => true
        ];

        $address = $this->repository->createForModel($user, $data);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertDatabaseHas('addresses', [
            'street' => 'Rua das Flores',
            'addressable_id' => $user->id
        ]);
    }

    /** @test */
    public function it_can_update_an_address()
    {
        $address = Address::factory()->create(['street' => 'Rua Antiga']);
        $data = ['street' => 'Rua Nova'];

        $result = $this->repository->update($address, $data);

        $this->assertEquals('Rua Nova', $result->street);
        $this->assertDatabaseHas('addresses', ['street' => 'Rua Nova']);
    }

    /** @test */
    public function it_can_find_an_address_by_id()
    {
        $address = Address::factory()->create();
        $found = $this->repository->findById($address->id);

        $this->assertNotNull($found);
        $this->assertEquals($address->id, $found->id);
    }

    /** @test */
    public function it_can_delete_an_address()
    {
        $address = Address::factory()->create();
        
        $this->repository->delete($address);

        // Si usas SoftDeletes en el modelo Address:
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
        
        // Si NO usas SoftDeletes, cambia la línea de arriba por:
        // $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }
}