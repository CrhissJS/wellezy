<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        // Simula una solicitud de registro
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Verifica que el usuario fue creado y se recibe la respuesta correcta
        $response->assertStatus(200)
                 ->assertJson(['message' => 'User registered successfully']);

        // Verifica que el usuario está en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_can_login()
    {
        // Crea un usuario en la base de datos
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Simula una solicitud de inicio de sesión
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Verifica que se recibe un token de acceso
        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_login_fails_with_incorrect_credentials()
    {
        // Simula una solicitud de inicio de sesión con credenciales incorrectas
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        // Verifica que se recibe un error de autenticación
        $response->assertStatus(401) // Cambia aquí a 401
                ->assertJson(['message' => 'The provided credentials are incorrect.']);
    }
}