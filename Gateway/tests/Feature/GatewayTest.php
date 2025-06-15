<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GatewayTest extends TestCase
{
    use DatabaseTransactions;

        public function test_login_correcto()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'], 
            ]);
    }

    public function test_autenticacion_incorrecta()
    {
        $response = $this->postJson('/api/login', [
            'usuario' => 'admin',
            'clave' => 'clave_incorrecta',
        ]);

        $response
            ->assertStatus(401) 
            ->assertSeeText('Credenciales incorrectas');
    }

        public function test_enrutamiento_a_prediction()
    {
        // Crear usuario
        $user = \App\Models\User::factory()->create();

        DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => 2,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/prediction', [
            'texto' => 'Texto de prueba',
        ]);

        $response->assertStatus(200);
    }


}
