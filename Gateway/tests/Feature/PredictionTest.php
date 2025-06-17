<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PredictionTest extends TestCase
{
    use DatabaseTransactions;

   public function test_envio_comentario_y_recibe_prediccion()
{
    $user = User::factory()->create();
    \DB::table('user_role')->insert([
        'user_id' => $user->id,
        'role_id' => 2,
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    // Fakear la URL correcta hacia Flask
    Http::fake([
        'http://127.0.0.1:5000/prediction' => Http::response([
            'prediction' => 'positivo'
        ], 200)
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->postJson('/api/prediction', [
        'comment' => 'happy'
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'prediction' => 'positivo'
             ]);

    // Verificar que se envió el request correcto
    Http::assertSent(function ($request) {
        $data = json_decode($request->body(), true);
        return $request->hasHeader('X-API-KEY', '1234') &&
               $request->url() === 'http://127.0.0.1:5000/prediction' &&
               $data === [
                   'comment' => 'happy'
               ];
    });
}
public function test_clasifica_comentario_largo()
{
    $user = User::factory()->create();
    \DB::table('user_role')->insert([
        'user_id' => $user->id,
        'role_id' => 2,
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    Http::fake([
        'http://127.0.0.1:5000/prediction' => Http::response([
            'prediction' => 'neutro'
        ], 200)
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->postJson('/api/prediction', [
        'comment' => str_repeat('table. ', 20) // comentario largo
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'prediction' => 'neutro'
             ]);

    Http::assertSent(function ($request) {
        $data = json_decode($request->body(), true);
        return $request->hasHeader('X-API-KEY', '1234') &&
               $request->url() === 'http://127.0.0.1:5000/prediction' &&
               isset($data['comment']);
    });
}
 use DatabaseTransactions;

    private function crearUsuarioConRol2()
    {
        $user = User::factory()->create();
        \DB::table('user_role')->insert([
            'user_id' => $user->id,
            'role_id' => 2,
        ]);
        return $user;
    }

    /** @test */
    public function clasifica_comentario_vacio()
    {
        $user = $this->crearUsuarioConRol2();
        $token = $user->createToken('api-token')->plainTextToken;

        Http::fake([
            'http://127.0.0.1:5000/prediction' => Http::response([
                'prediction' => 'neutro'
            ], 200)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/prediction', [
            'comment' => ''
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'prediction' => 'neutro'
                 ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:5000/prediction';
        });
    }


}
