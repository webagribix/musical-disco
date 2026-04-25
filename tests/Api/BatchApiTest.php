<?php
declare(strict_types=1);
namespace Tests\Api;

use Tests\TestCase;

/**
 * Integration tests for Batch API endpoints.
 * Requires a running MySQL test DB and app reachable via HTTP.
 * Skip if APP_TEST_BASE_URL not set.
 */
class BatchApiTest extends TestCase
{
    private static string $baseUrl;
    private static string $token = '';

    public static function setUpBeforeClass(): void
    {
        self::$baseUrl = getenv('APP_TEST_BASE_URL') ?: '';
    }

    private function skip(): void
    {
        if (empty(self::$baseUrl)) {
            $this->markTestSkipped('APP_TEST_BASE_URL not set — skipping HTTP integration tests');
        }
    }

    public function testLoginReturnsToken(): void
    {
        $this->skip();
        $res = $this->post('/api/v1/auth/login', ['email' => 'admin@farm.test', 'password' => 'password']);
        $this->assertSame(200, $res['status']);
        $this->assertArrayHasKey('token', $res['body']);
        self::$token = $res['body']['token'];
    }

    public function testListBatchesRequiresAuth(): void
    {
        $this->skip();
        $res = $this->get('/api/v1/batches');
        $this->assertSame(401, $res['status']);
    }

    public function testCreateBatchValidation(): void
    {
        $this->skip();
        $res = $this->post('/api/v1/batches', [], self::$token);
        $this->assertSame(422, $res['status']);
    }

    private function get(string $path, string $token = ''): array
    {
        return $this->request('GET', $path, null, $token);
    }

    private function post(string $path, array $body, string $token = ''): array
    {
        return $this->request('POST', $path, $body, $token);
    }

    private function request(string $method, string $path, ?array $body, string $token): array
    {
        $ch = curl_init(self::$baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => array_filter([
                'Content-Type: application/json',
                'Accept: application/json',
                $token ? "Authorization: Bearer $token" : null,
            ]),
            CURLOPT_POSTFIELDS     => $body ? json_encode($body) : null,
            CURLOPT_TIMEOUT        => 10,
        ]);
        $raw    = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['status' => $status, 'body' => json_decode($raw ?: '{}', true) ?? []];
    }
}
