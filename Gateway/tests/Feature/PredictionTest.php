<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PredictionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuración específica para el FlaskController
        config(['services.flask.url' => 'http://sistema:5000']);
        config(['services.flask.key' => '1234']);
        
        // Mock de las respuestas del microservicio Flask
        Http::fake([
            'http://sistema:5000/prediction' => Http::response([
                'prediction' => 'positivo' // Valor por defecto
            ], 200),
        ]);
    }

    /** @test */
    public function test_envio_comentario_y_recibe_prediccion()
    {
        $user = $this->createUserWithRole(2);
        $token = $user->createToken('api-token')->plainTextToken;

        // Mock específico para este test
        Http::fake([
            'http://sistema:5000/prediction' => Http::response([
                'prediction' => 'positivo'
            ], 200),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'happy',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'prediction' => 'positivo'
            ]);
    }

    /** @test */
    public function test_clasifica_comentario_largo()
    {
        $user = $this->createUserWithRole(2);
        $token = $user->createToken('api-token')->plainTextToken;

        $comentarioLargo = str_repeat('happy ', 100);

        // Mock para comentario largo
        Http::fake([
            'http://sistema:5000/prediction' => Http::response([
                'prediction' => 'positivo'
            ], 200),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => $comentarioLargo,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'prediction'
            ]);
    }

    /** @test */
    public function test_clasifica_comentario_positivo()
    {
        $user = $this->createUserWithRole(2);
        $token = $user->createToken('api-token')->plainTextToken;

        // Mock específico para comentario positivo
        Http::fake([
            'http://sistema:5000/prediction' => Http::response([
                'prediction' => 'positivo'
            ], 200),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'good',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'prediction' => 'positivo'
            ]);
    }

    /** @test */
    public function test_clasifica_comentario_neutro()
    {
        $user = $this->createUserWithRole(2);
        $token = $user->createToken('api-token')->plainTextToken;

        // Mock específico para comentario neutro
        Http::fake([
            'http://sistema:5000/prediction' => Http::response([
                'prediction' => 'neutro'
            ], 200),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'book',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'prediction' => 'neutro'
            ]);
    }

    /** @test */
    public function test_manejo_de_error_del_microservicio()
    {
        $user = $this->createUserWithRole(2);
        $token = $user->createToken('api-token')->plainTextToken;

        // Mock de error del microservicio
        Http::fake([
            'http://sistema:5000/prediction' => Http::response([
                'error' => 'Servicio no disponible'
            ], 503),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'test',
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'error' => 'Servicio no disponible'
            ]);
    }

    private function createUserWithRole(int $roleId): User
    {
        $user = User::factory()->create();
        DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => $roleId,
        ]);
        return $user;
    }
}