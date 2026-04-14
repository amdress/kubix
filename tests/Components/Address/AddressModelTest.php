<?php

namespace Tests\Components\Address;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AddressModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_morphed_to_a_user()
    {
        // Creamos un usuario y le asociamos una dirección
        $user = User::factory()->create();
        
        $address = Address::create([
            'addressable_id' => $user->id,
            'addressable_type' => User::class,
            'zip_code' => '01001000',
            'street' => 'Praça da Sé',
            'number' => 's/n',
            'neighborhood' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
            'country' => 'BR',
            'is_primary' => true,
        ]);

        $this->assertInstanceOf(User::class, $address->addressable);
        $this->assertEquals($user->id, $address->addressable->id);
    }

    #[Test]
    public function it_formats_the_zip_code_correctly()
    {
        $address = new Address(['zip_code' => '12345678']);
        
        // Probamos el accessor formatted_zip_code
        $this->assertEquals('12345-678', $address->formatted_zip_code);
    }

    #[Test]
    public function it_returns_the_full_formatted_address()
    {
        $address = new Address([
            'street' => 'Rua Flores',
            'number' => '100',
            'neighborhood' => 'Centro',
            'city' => 'Curitiba',
            'state' => 'PR',
            'zip_code' => '80000000',
            'country' => 'BR'
        ]);

        $expected = 'Rua Flores, 100, Centro, Curitiba, PR, 80000-000';
        
        $this->assertEquals($expected, $address->full_address);
    }

    #[Test]
    public function it_can_filter_primary_addresses_using_scope()
    {
        $user = User::factory()->create();
        
        // Una primaria, una no primaria
        Address::factory()->create(['is_primary' => true, 'addressable_id' => $user->id, 'addressable_type' => User::class]);
        Address::factory()->create(['is_primary' => false, 'addressable_id' => $user->id, 'addressable_type' => User::class]);

        $this->assertEquals(1, Address::primary()->count());
    }

    #[Test]
    public function it_checks_if_it_has_coordinates()
    {
        $addressWithCoord = new Address(['latitude' => -23.55, 'longitude' => -46.63]);
        $addressWithoutCoord = new Address(['latitude' => null, 'longitude' => null]);

        $this->assertTrue($addressWithCoord->hasCoordinates());
        $this->assertFalse($addressWithoutCoord->hasCoordinates());
    }

    #[Test]
    public function it_handles_soft_deletes()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'addressable_id' => $user->id, 
            'addressable_type' => User::class
        ]);

        $address->delete();

        $this->assertSoftDeleted($address);
        $this->assertDatabaseMissing('addresses', ['id' => $address->id, 'deleted_at' => null]);
    }
}