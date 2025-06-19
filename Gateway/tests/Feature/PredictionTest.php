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

    public function test_envio_comentario_y_recibe_prediccion()
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
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'happy',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'prediction',
            ]);
    }


    public function test_clasifica_comentario_largo()
    {
        $user = \App\Models\User::factory()->create();

        DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => 2,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $comentarioLargo = str_repeat('table. ', 20);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => $comentarioLargo,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'prediction',
            ]);
    }

    use DatabaseTransactions;

    private function crearUsuarioConRol2()
    {
        $user = User::factory()->create();
        DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => 2,
        ]);
        return $user;
    }

    /** @test */

    /** @test */
    public function test_clasifica_comentario_positivo()
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
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'good',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'prediction',
            ]);
    }

    /** @test */
    public function test_clasifica_comentario_neutro()
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
            'X-API-KEY' => env('API_KEY'),
        ])->postJson('/api/prediction', [
            'comment' => 'book',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'prediction' => 'neutro',
            ]);
    }
}
