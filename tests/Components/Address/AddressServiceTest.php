<?php

namespace Tests\Unit\Components\Address;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Components\Address\AddressService;
use App\Components\Address\AddressRepository;
use App\Components\Address\DTO\AddressDto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AddressService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AddressService(new AddressRepository());
    }

    /** @test */
    public function it_can_create_address_using_full_dto()
    {
        $user = User::factory()->create();
        
        // DTO con los 11 parámetros exactos que pide tu constructor
        $dto = AddressDto::from([
            'street'       => 'Avenida Central',
            'number'       => '500',
            'neighborhood' => 'Bairro Alto',
            'complement'   => 'Sala 2',
            'city'         => 'Curitiba',
            'state'        => 'PR',
            'zip_code'     => '80000-000',
            'country'      => 'Brazil',
            'latitude'     => -25.4296,
            'longitude'    => -49.2719,
            'is_primary'   => true
        ]);

        $address = $this->service->createFor($user, $dto);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('Avenida Central', $address->street);
        $this->assertDatabaseHas('addresses', ['addressable_id' => $user->id]);
    }

    /** @test */
    public function it_can_update_address_via_service()
    {
        $address = Address::factory()->create();
        $dto = AddressDto::from(array_merge($address->toArray(), ['street' => 'Calle Actualizada']));

        $updated = $this->service->update($address, $dto);

        $this->assertEquals('Calle Actualizada', $updated->street);
    }

    /** @test */
    public function it_can_find_and_delete_address()
    {
        $address = Address::factory()->create();

        $found = $this->service->find($address->id);
        $this->assertNotNull($found);

        $this->service->delete($address);
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
    }
}