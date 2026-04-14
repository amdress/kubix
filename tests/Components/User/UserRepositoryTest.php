<?php

namespace Tests\Unit\Components\User;

use Tests\TestCase;
use App\Models\User;
use App\Components\User\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $data = [
            'name'     => 'Stoic Dev',
            'email'    => 'stoic@example.com',
            'password' => 'secret123',
            'cpf'      => '12345678900'
        ];

        $user = $this->repository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'stoic@example.com']);
        // Verificamos que el repo (o el modelo) haya hecho el hash
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    /** @test */
    public function it_can_update_user_data()
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        
        $updated = $this->repository->update($user, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        
        $user = User::factory()->create();
        $this->repository->delete($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}