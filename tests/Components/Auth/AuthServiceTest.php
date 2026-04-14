<?php
namespace Tests\Components\Auth;

use App\Models\User;
use App\Components\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();

        // Crear el rol que el servicio asigna por defecto en register()
        Role::create(['name' => 'responsable_company', 'guard_name' => 'web']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_authenticates_a_user_and_returns_token_and_permissions()
    {
        $password = 'secret-123';
        $user     = User::factory()->create([
            'email'    => 'login@test.com',
            'password' => Hash::make($password),
        ]);

        $result = $this->authService->authenticate('login@test.com', $password, 'iPhone de Test');

        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertNotNull($result['token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_validation_exception_on_invalid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'wrong@test.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->expectException(ValidationException::class);

        $this->authService->authenticate('wrong@test.com', 'wrong-password');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_registers_a_new_user_and_assigns_default_role()
    {
        $data = [
            'name'     => 'New User',
            'email'    => 'new@test.com',
            'password' => 'password123',
        ];

        $user = $this->authService->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
        $this->assertTrue($user->hasRole('responsable_company'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
public function it_revokes_api_token_on_logout()
{
    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    // 1. Creamos el token
    $tokenInstance = $user->createToken('test-token');
    $plainTextToken = $tokenInstance->plainTextToken;

    // 2. IMPORTANTE: Le decimos al usuario que este es su token actual para el test
    $user->withAccessToken($tokenInstance->accessToken);

    $this->actingAs($user, 'sanctum');

    $request = Request::create('/logout', 'POST');
    
    // Simular sesión para evitar el error anterior
    $session = app('session')->driver('array');
    $request->setLaravelSession($session);
    $request->setUserResolver(fn () => $user);

    $response = $this->authService->logout($request);

    // 3. Verificaciones
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertStringContainsString('API token revoked', $response->getContent());
    
    // Verificamos que realmente se borró de la base de datos
    $this->assertCount(0, $user->fresh()->tokens);
}

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_if_logout_is_called_without_user()
    {
        $request = Request::create('/logout', 'POST');

        $response = $this->authService->logout($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('No authenticated user', $response->getContent());
    }
}
