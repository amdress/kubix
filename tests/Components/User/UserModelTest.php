<?php

namespace Tests\Components\User;

use App\Models\User;
use App\Models\Address;
use App\Models\Affiliation;
use App\Models\DeviceToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_expected_columns_in_database()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id', 'name', 'email', 'password', 'cpf', 'phone', 
                'blocked_at', 'registered_by', 'deleted_at'
            ])
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_casts_attributes_to_correct_types()
    {
        $user = new User();
        $user->email_verified_at = '2025-01-01 10:00:00';
        $user->blocked_at = '2025-12-24 10:00:00';
        $user->password = 'secret';

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->blocked_at);
        $this->assertEquals('hashed', $user->getCasts()['password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_hides_sensitive_attributes_from_serialization()
    {
        $user = new User();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    // #[\PHPUnit\Framework\Attributes\Test]
    // public function it_has_a_morph_one_address_relation()
    // {
    //     $user = User::factory()->create();
    //     $address = Address::factory()->make();
    //     $user->address()->save($address);

    //     $this->assertInstanceOf(Address::class, $user->fresh()->address);
    //     $this->assertEquals($user->id, $user->address->addressable_id);
    //     $this->assertEquals(get_class($user), $user->address->addressable_type);
    // }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_have_many_affiliations()
    {
        $user = User::factory()->create();
        
        // Agregamos affiliatable_type y affiliatable_id si es polimórfica
        // O tenant_id si es para un sistema SaaS
        Affiliation::create([
            'user_id' => $user->id,
            'role' => 'admin',
            'affiliatable_id' => 1, 
            'affiliatable_type' => 'App\Models\Company', // Ajusta según tu lógica
        ]);

        $this->assertCount(1, $user->fresh()->affiliations);
        $this->assertInstanceOf(Affiliation::class, $user->affiliations->first());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_have_many_device_tokens()
    {
        $user = User::factory()->create();
        
        DeviceToken::create([
            'user_id' => $user->id,
            'token' => 'expo-token-123',
            'provider' => 'expo', // Campo requerido según el error
            'platform' => 'ios',
        ]);

        $this->assertCount(1, $user->fresh()->deviceTokens);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_belongs_to_a_creator()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create(['registered_by' => $admin->id]);

        $this->assertInstanceOf(User::class, $user->creator);
        $this->assertEquals($admin->id, $user->creator->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_implements_soft_deletes()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertNotNull($user->deleted_at);
    }
}