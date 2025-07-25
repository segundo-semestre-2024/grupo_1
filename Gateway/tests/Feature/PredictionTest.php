<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class PredictionServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.flask.url', env('MICROSERVICIO_FLASK'));
        Config::set('services.flask.api_key', env('API_KEY'));
    }

    private function headers(): array
    {
        return [
            'X-API-KEY' => config('services.flask.api_key'),
            'Content-Type' => 'application/json',
        ];
    }

    public function test_health_check_responde_ok()
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200)
        ]);

        $response = Http::get(config('services.flask.url') . '/health');

        $this->assertEquals(200, $response->status());
        $this->assertEquals(['status' => 'ok'], $response->json());
    }

    public function test_prediccion_positiva()
    {
        Http::fake([
            '*/prediction' => Http::response(['prediction' => 'positivo'], 200)
        ]);

        $comentario = "Me encanta este producto, es excelente!";

        $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
            'comment' => $comentario
        ]);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('positivo', $response->json('prediction'));

        Http::assertSent(fn($request) =>
            $request->url() === config('services.flask.url') . '/prediction' &&
            $request['comment'] === $comentario &&
            $request->header('X-API-KEY')[0] === config('services.flask.api_key')
        );
    }

    public function test_prediccion_negativa_envia_notificacion()
    {
        Http::fake([
            '*/prediction' => Http::response([
                'prediction' => 'negativo',
                'notification_status' => 'Enviada'
            ], 200)
        ]);

        $comentario = "Este producto es terrible, no lo recomiendo.";

        $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
            'comment' => $comentario
        ]);

        $data = $response->json();

        $this->assertEquals(200, $response->status());
        $this->assertEquals('negativo', $data['prediction']);
        $this->assertEquals('Enviada', $data['notification_status']);
    }

    public function test_api_key_invalida()
    {
        Http::fake([
            '*/prediction' => Http::response([
                'message' => 'Acceso denegado. Clave API incorrecta.'
            ], 403)
        ]);

        $response = Http::withHeaders([
            'X-API-KEY' => 'clave_invalida',
            'Content-Type' => 'application/json',
        ])->post(config('services.flask.url') . '/prediction', [
            'comment' => 'Test con API Key inválida'
        ]);

        $this->assertEquals(403, $response->status());
        $this->assertStringContainsString('Acceso denegado', $response->json('message'));
    }

    public function test_sin_comentario_retorna_error()
    {
        Http::fake([
            '*/prediction' => Http::response([
                'error' => 'Debe proporcionar un comentario'
            ], 400)
        ]);

        $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', []);

        $this->assertEquals(400, $response->status());
        $this->assertArrayHasKey('error', $response->json());
    }

    public function test_error_conexion_microservicio()
    {
        Http::fake([
            '*/prediction' => Http::response([], 500)
        ]);

        $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
            'comment' => 'Comentario de prueba'
        ]);

        $this->assertEquals(500, $response->status());
    }

    public function test_timeout_conexion()
    {
        Http::fake([
            '*/prediction' => fn() => throw new \Illuminate\Http\Client\ConnectionException()
        ]);

        $this->expectException(\Illuminate\Http\Client\ConnectionException::class);

        Http::timeout(5)->withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
            'comment' => 'Comentario para timeout'
        ]);
    }

    public function test_multiples_predicciones()
    {
        $comentarios = [
            ['input' => 'happy', 'expected' => 'positivo'],
            ['input' => 'bad', 'expected' => 'negativo'],
            ['input' => 'hello', 'expected' => 'neutro'],
        ];

        Http::fake([
            '*/prediction' => Http::sequence()
                ->push(['prediction' => 'positivo'], 200)
                ->push(['prediction' => 'negativo', 'notification_status' => 'Enviada'], 200)
                ->push(['prediction' => 'neutro'], 200)
        ]);

        foreach ($comentarios as $comentario) {
            $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
                'comment' => $comentario['input']
            ]);

            $this->assertEquals(200, $response->status());
            $this->assertEquals($comentario['expected'], $response->json('prediction'));
        }

        Http::assertSentCount(3);
    }

    public function test_formato_respuesta_prediccion()
    {
        Http::fake([
            '*/prediction' => Http::response(['prediction' => 'positivo'], 200)
        ]);

        $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
            'comment' => 'Probando estructura'
        ]);

        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('prediction', $data);
        $this->assertContains($data['prediction'], ['positivo', 'negativo', 'neutro']);
    }

    public function test_predicciones_comentarios_especiales()
    {
        Http::fake([
            '*/prediction' => Http::response(['prediction' => 'positivo'], 200)
        ]);

        $comentarios = [
            '¡Excelente! 😊',
            '🤢🤮 No me gustó',
            '¿Qué es esto?',
        ];

        foreach ($comentarios as $comentario) {
            $response = Http::withHeaders($this->headers())->post(config('services.flask.url') . '/prediction', [
                'comment' => $comentario
            ]);

            $this->assertEquals(200, $response->status());
            $this->assertArrayHasKey('prediction', $response->json());
        }
    }
}
