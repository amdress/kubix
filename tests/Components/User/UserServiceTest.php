<?php

namespace Tests\Unit\Components\User;

use Tests\TestCase;
use App\Models\User;
use App\Components\User\UserService;
use App\Components\User\UserRepository;
use App\Components\User\DTO\UserDto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService(new UserRepository());
    }

    /** @test */
    public function it_creates_user_via_service_dto()
    {
        $dto = UserDto::from([
            'name'     => 'Acuario User',
            'email'    => 'aquarius@test.com',
            'password' => 'password123',
            'cpf'      => '98765432100',
            'phone'    => '554199999999'
        ]);

        $user = $this->service->createUser($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('aquarius@test.com', $user->email);
    }
}