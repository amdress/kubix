<?php

namespace Tests\Components\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Evita errores de assets si el login redirige a alguna vista por error
        $this->withoutVite();
        
        // Crear el rol necesario
        Role::create(['name' => 'responsable_company', 'guard_name' => 'web']);
    }

    #[Test]
    public function a_user_can_login_with_valid_credentials()
    {
        $password = 'SafePassword@2025';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => $password,
            'device_name' => 'TestDevice'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['user', 'token', 'roles', 'permissions']
            ]);
            
        $this->assertNotEmpty($response->json('data.token'));
    }

    #[Test]
    public function a_user_cannot_register_with_invalid_data()
    {
        // Caso: Password no cumple con la validación estricta que vimos en el DTO
        $userData = [
            'name' => 'Juan',
            'email' => 'email-invalido', // Email mal formado
            'password' => '123',         // Muy corta y sin símbolos
            'password_confirmation' => '456' // No coincide
        ];

        $response = $this->postJson('/api/register', $userData);

        // Debe retornar error de validación
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    #[Test]
    public function a_user_can_register_successfully()
    {
        $userData = [
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'password' => 'Secret@123', 
            'password_confirmation' => 'Secret@123',
            'cpf' => '12345678901',
            'phone' => '123456789'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Usuario registrado correctamente');

        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
        
        // Verificar que se le asignó el rol por defecto
        $user = User::where('email', 'juan@example.com')->first();
        $this->assertTrue($user->hasRole('responsable_company'));
    }

    #[Test]
    public function registration_fails_if_email_already_exists()
    {
        User::factory()->create(['email' => 'duplicado@example.com']);

        $userData = [
            'name' => 'Otro Juan',
            'email' => 'duplicado@example.com',
            'password' => 'Secret@123',
            'password_confirmation' => 'Secret@123',
            'cpf' => '00000000000',
            'phone' => '999999999'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function a_user_can_logout()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Enviamos el token en el header
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Sesión cerrada correctamente');
            
        $this->assertCount(0, $user->fresh()->tokens);
    }
}