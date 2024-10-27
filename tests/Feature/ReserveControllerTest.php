<?php

namespace Tests\Feature;

use App\Models\User; // Asegúrate de importar el modelo User
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear un usuario para las pruebas
        $this->user = User::factory()->create([
            'password' => bcrypt('password'), // Asegúrate de establecer una contraseña
        ]);
    }

    public function test_can_create_reserve()
    {
        $this->actingAs($this->user, 'sanctum');
        // Simula un request de reserva
        $response = $this->postJson('/api/reserves', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'passenger_count' => 1,
            'adult_count' => 1,
            'child_count' => 0,
            'baby_count' => 0,
            'total_amount' => 100.00,
            'currency' => 'COP',
            'itineraries' => [
                [
                    'departure_city' => 'MDE',
                    'arrival_city' => 'YYZ',
                    'departure_date' => '2024-10-31',
                    'arrival_date' => '2024-10-31',
                    'departure_time' => '05:00',
                    'arrival_time' => '10:00',
                    'flight_number' => 'AV123',
                    'marketing_carrier' => 'AV'
                ]
            ]
        ]);

        // Verifica que la reserva fue creada
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message', 
                'data' => [
                    'id', 
                    'name', 
                    'email', 
                    'passenger_count', 
                    'adult_count', 
                    'child_count', 
                    'baby_count', 
                    'total_amount', 
                    'currency', 
                    'itineraries' => [
                        '*' => [
                            'departure_city', 
                            'arrival_city', 
                            'departure_date', 
                            'arrival_date', 
                            'departure_time', 
                            'arrival_time', 
                            'flight_number', 
                            'marketing_carrier'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('reserves', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'passenger_count' => 1,
            'total_amount' => 100.00,
            'currency' => 'COP',
        ]);

        $this->assertDatabaseHas('itineraries', [
            'departure_city' => 'MDE',
            'arrival_city' => 'YYZ',
            'flight_number' => 'AV123',
        ]);
    }

}
