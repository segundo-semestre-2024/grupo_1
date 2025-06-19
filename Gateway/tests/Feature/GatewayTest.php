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
        $user = \App\Models\User::factory()->create();

        DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => 2,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),  // asegúrate que tu .env la tiene
        ])->postJson('/api/prediction', [
            'comment' => 'Texto de prueba',
        ]);


        $response->assertStatus(200);
    }


    public function test_admin_puede_listar_usuarios(): void
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123')
        ]);

        DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => 1, 
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('token');

        // Hacer solicitud GET a /list-user con token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/list-user');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Lista de usuarios obtenida correctamente.'
                 ])
                 ->assertJsonStructure([
                     'usuarios' => [
                         '*' => ['id', 'name', 'email'] // Ajusta según lo que devuelvas
                     ]
                 ]);
    }

}
